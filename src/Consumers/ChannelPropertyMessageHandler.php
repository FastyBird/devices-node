<?php declare(strict_types = 1);

/**
 * ChannelPropertyMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 * @since          0.1.0
 *
 * @date           19.03.20
 */

namespace FastyBird\DevicesNode\Consumers;

use Doctrine\Common;
use Doctrine\DBAL;
use Doctrine\DBAL\Connection;
use FastyBird\DevicesNode;
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
 * Channel property message consumer
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelPropertyMessageHandler implements NodeExchangeConsumers\IMessageHandler
{

	use Nette\SmartObject;
	use TPropertyMessageHandler;

	/** @var Models\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var Models\Channels\IChannelRepository */
	private $channelRepository;

	/** @var Models\Channels\Properties\IPropertiesManager */
	private $propertiesManager;

	/** @var Models\States\Channels\IPropertiesManager */
	private $propertiesStatesManager;

	/** @var Models\States\Channels\IPropertyRepository */
	private $propertyStateRepository;

	/** @var NodeMetadataLoaders\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Channels\IChannelRepository $channelRepository,
		Models\Channels\Properties\IPropertiesManager $propertiesManager,
		Models\States\Channels\IPropertiesManager $propertiesStatesManager,
		Models\States\Channels\IPropertyRepository $propertyStateRepository,
		NodeMetadataLoaders\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger,
		Common\Persistence\ManagerRegistry $managerRegistry
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;
		$this->propertiesManager = $propertiesManager;
		$this->propertiesStatesManager = $propertiesStatesManager;
		$this->propertyStateRepository = $propertyStateRepository;

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
			$this->logger->error(sprintf('[CONSUMER] Device channel "%s" is not registered', $message->offsetGet('device')));

			return true;
		}

		$property = $channel->findProperty($message->offsetGet('property'));

		if ($property === null) {
			$this->logger->error(sprintf('[CONSUMER] Property "%s" is not registered', $message->offsetGet('property')));

			return true;
		}

		if (
			$message->offsetExists('expected')
			&& $message->offsetExists('value')
		) {
			$this->logger->error('[CONSUMER] Message format is invalid. Message can\'t contain both expected value & current value');

			return true;
		}

		try {
			switch ($routingKey) {
				case DevicesNode\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY:
					// Start transaction connection to the database
					$this->getOrmConnection()->beginTransaction();

					$toUpdate = $this->handlePropertyConfiguration($message);

					$this->propertiesManager->update($property, Utils\ArrayHash::from($toUpdate));

					if (
						$message->offsetExists('expected')
						|| $message->offsetExists('value')
					) {
						// Property have to be configured & have to be settable
						if ($property->isSettable()) {
							$propertyState = $this->propertyStateRepository->findOne($property->getId());

							if ($propertyState !== null) {
								$toUpdate = $this->handlePropertyState($propertyState, $message);

								$this->propertiesStatesManager->updateState(
									$propertyState,
									$property,
									Utils\ArrayHash::from($toUpdate)
								);
							}
						}
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
		switch ($routingKey) {
			case DevicesNode\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY:
				if ($origin === DevicesNode\Constants::NODE_MQTT_ORIGIN) {
					return $this->schemaLoader->load(NodeMetadata\Constants::RESOURCES_FOLDER . '/schemas/mqtt-node/data.channel.property.json');

				} elseif (
					$origin === DevicesNode\Constants::NODE_TRIGGERS_ORIGIN
					|| $origin === DevicesNode\Constants::NODE_WEBSOCKETS_ORIGIN
				) {
					return $this->schemaLoader->load(NodeMetadata\Constants::RESOURCES_FOLDER . '/schemas/data/data.channel.property.json');
				}
		}

		return null;
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
