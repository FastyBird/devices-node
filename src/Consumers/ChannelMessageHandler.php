<?php declare(strict_types = 1);

/**
 * ChannelMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 * @since          0.1.0
 *
 * @date           18.03.20
 */

namespace FastyBird\DevicesNode\Consumers;

use Doctrine\Common;
use Doctrine\DBAL;
use Doctrine\DBAL\Connection;
use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Queries;
use FastyBird\NodeExchange\Consumers as NodeExchangeConsumers;
use FastyBird\NodeExchange\Exceptions as NodeExchangeExceptions;
use FastyBird\NodeMetadata;
use FastyBird\NodeMetadata\Loaders as NodeMetadataLoaders;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Channel message consumer
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelMessageHandler implements NodeExchangeConsumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var Models\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var Models\Channels\IChannelRepository */
	private $channelRepository;

	/** @var Models\Channels\IChannelsManager */
	private $channelsManager;

	/** @var Models\Channels\Properties\IPropertiesManager */
	private $channelPropertiesManager;

	/** @var Models\Channels\Controls\IControlsManager */
	private $channelControlManager;

	/** @var NodeMetadataLoaders\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Channels\IChannelRepository $channelRepository,
		Models\Channels\IChannelsManager $channelsManager,
		Models\Channels\Properties\IPropertiesManager $channelPropertiesManager,
		Models\Channels\Controls\IControlsManager $channelControlManager,
		NodeMetadataLoaders\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger,
		Common\Persistence\ManagerRegistry $managerRegistry
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;
		$this->channelsManager = $channelsManager;
		$this->channelPropertiesManager = $channelPropertiesManager;
		$this->channelControlManager = $channelControlManager;

		$this->schemaLoader = $schemaLoader;
		$this->logger = $logger;
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws DBAL\ConnectionException
	 * @throws NodeExchangeExceptions\TerminateException
	 */
	public function process(
		string $routingKey,
		Utils\ArrayHash $message
	): bool {
		try {
			$findQuery = new Queries\FindDevicesQuery();
			$findQuery->byIdentifier($message->offsetGet('device'));

			$device = $this->deviceRepository->findOneBy($findQuery);

		} catch (Throwable $ex) {
			throw new NodeExchangeExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($device === null) {
			$this->logger->error(sprintf('[CONSUMER] Device "%s" is not registered', $message->offsetGet('device')));

			return true;
		}

		try {
			$findQuery = new Queries\FindChannelsQuery();
			$findQuery->forDevice($device);
			$findQuery->byChannel($message->offsetGet('channel'));

			$channel = $this->channelRepository->findOneBy($findQuery);

		} catch (Throwable $ex) {
			throw new NodeExchangeExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($channel === null) {
			$this->logger->error(sprintf('[CONSUMER] Device channel "%s" is not registered', $message->offsetGet('channel')));

			return true;
		}

		try {
			switch ($routingKey) {
				case DevicesNode\Constants::RABBIT_MQ_CHANNELS_DATA_ROUTING_KEY:
					// Start transaction connection to the database
					$this->getOrmConnection()->beginTransaction();

					$toUpdate = [];

					if ($message->offsetExists('name')) {
						$toUpdate['name'] = $message->offsetGet('name');
					}

					if ($message->offsetExists('properties')) {
						$this->setChannelProperties($channel, $message->offsetGet('properties'));
					}

					if ($message->offsetExists('control')) {
						$this->setChannelControl($channel, $message->offsetGet('control'));
					}

					if ($toUpdate !== []) {
						$this->channelsManager->update($channel, Utils\ArrayHash::from($toUpdate));
					}

					// Commit all changes into database
					$this->getOrmConnection()->commit();
					break;

				default:
					throw new Exceptions\InvalidStateException('Unknown routing key');
			}

		} catch (Exceptions\InvalidStateException $ex) {
			return false;

		} catch (Throwable $ex) {
			throw new NodeExchangeExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);

		} finally {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}
		}

		$this->logger->info('[CONSUMER] Successfully consumed entity message');

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSchema(string $routingKey, string $origin): ?string
	{
		if ($origin === DevicesNode\Constants::NODE_MQTT_ORIGIN) {
			switch ($routingKey) {
				case DevicesNode\Constants::RABBIT_MQ_CHANNELS_DATA_ROUTING_KEY:
					return $this->schemaLoader->load(NodeMetadata\Constants::RESOURCES_FOLDER . '/schemas/mqtt-node/data.channel.json');
			}
		}

		return null;
	}

	/**
	 * @param Entities\Channels\IChannel $channel
	 * @param Utils\ArrayHash<string> $properties
	 *
	 * @return void
	 */
	private function setChannelProperties(
		Entities\Channels\IChannel $channel,
		Utils\ArrayHash $properties
	): void {
		foreach ($properties as $propertyName) {
			if (!$channel->hasProperty($propertyName)) {
				$this->channelPropertiesManager->create(Utils\ArrayHash::from([
					'channel'  => $channel,
					'property' => $propertyName,
				]));
			}
		}

		// Cleanup for unused properties
		foreach ($channel->getProperties() as $property) {
			if (!in_array($property->getProperty(), (array) $properties, true)) {
				$this->channelPropertiesManager->delete($property);
			}
		}
	}

	/**
	 * @param Entities\Channels\IChannel $channel
	 * @param Utils\ArrayHash<string> $controls
	 *
	 * @return void
	 */
	private function setChannelControl(
		Entities\Channels\IChannel $channel,
		Utils\ArrayHash $controls
	): void {
		$availableControls = [
			DevicesNode\Constants::CONTROL_CONFIG,
		];

		foreach ($controls as $controlName) {
			if (in_array($controlName, $availableControls, true)) {
				if (!$channel->hasControl($controlName)) {
					$this->channelControlManager->create(Utils\ArrayHash::from([
						'channel' => $channel,
						'name'    => $controlName,
					]));
				}
			}
		}

		// Cleanup for unused control
		foreach ($channel->getControls() as $control) {
			if (!in_array($control->getName(), (array) $controls, true)) {
				$this->channelControlManager->delete($control);
			}
		}
	}

	/**
	 * @return Connection
	 */
	protected function getOrmConnection(): Connection
	{
		$connection = $this->managerRegistry->getConnection();

		if ($connection instanceof Connection) {
			return $connection;
		}

		throw new Exceptions\RuntimeException('Entity manager could not be loaded');
	}

}
