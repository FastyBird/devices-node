<?php declare(strict_types = 1);

/**
 * ChannelMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Handlers
 * @since          0.1.0
 *
 * @date           04.12.20
 */

namespace FastyBird\DevicesNode\Handlers\MQTT;

use Doctrine\Common;
use Doctrine\DBAL;
use Doctrine\DBAL\Connection;
use FastyBird\DevicesModule;
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\MqttPlugin\Entities as MqttPluginEntities;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Device channel attributes MQTT message handler
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Handlers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelMessageHandler
{

	use Nette\SmartObject;

	/** @var DevicesModuleModels\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var DevicesModuleModels\Channels\IChannelRepository */
	private $channelRepository;

	/** @var DevicesModuleModels\Channels\IChannelsManager */
	private $channelsManager;

	/** @var DevicesModuleModels\Channels\Properties\IPropertiesManager */
	private $channelPropertiesManager;

	/** @var DevicesModuleModels\Channels\Controls\IControlsManager */
	private $channelControlManager;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		DevicesModuleModels\Devices\IDeviceRepository $deviceRepository,
		DevicesModuleModels\Channels\IChannelRepository $channelRepository,
		DevicesModuleModels\Channels\IChannelsManager $channelsManager,
		DevicesModuleModels\Channels\Properties\IPropertiesManager $channelPropertiesManager,
		DevicesModuleModels\Channels\Controls\IControlsManager $channelControlManager,
		Common\Persistence\ManagerRegistry $managerRegistry,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;
		$this->channelsManager = $channelsManager;
		$this->channelPropertiesManager = $channelPropertiesManager;
		$this->channelControlManager = $channelControlManager;

		$this->managerRegistry = $managerRegistry;

		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws DBAL\ConnectionException
	 * @throws Exceptions\InvalidStateException
	 */
	public function process(
		MqttPluginEntities\ChannelAttribute $entity
	): void {
		try {
			$findQuery = new DevicesModuleQueries\FindDevicesQuery();
			$findQuery->byIdentifier($entity->getDevice());

			$device = $this->deviceRepository->findOneBy($findQuery);

		} catch (Throwable $ex) {
			throw new Exceptions\InvalidStateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($device === null) {
			$this->logger->error(sprintf('[FB:NODE:MQTT] Device "%s" is not registered', $entity->getDevice()));

			return;
		}

		try {
			$findQuery = new DevicesModuleQueries\FindChannelsQuery();
			$findQuery->forDevice($device);
			$findQuery->byChannel($entity->getChannel());

			$channel = $this->channelRepository->findOneBy($findQuery);

		} catch (Throwable $ex) {
			throw new Exceptions\InvalidStateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($channel === null) {
			$this->logger->error(sprintf('[FB:NODE:MQTT] Device channel "%s" is not registered', $entity->getChannel()));

			return;
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$toUpdate = [];

			if ($entity->getAttribute() === MqttPluginEntities\Attribute::NAME) {
				$toUpdate['name'] = $entity->getValue();
			}

			if ($entity->getAttribute() === MqttPluginEntities\Attribute::PROPERTIES && is_array($entity->getValue())) {
				$this->setChannelProperties($channel, Utils\ArrayHash::from($entity->getValue()));
			}

			if ($entity->getAttribute() === MqttPluginEntities\Attribute::CONTROL && is_array($entity->getValue())) {
				$this->setChannelControl($channel, Utils\ArrayHash::from($entity->getValue()));
			}

			if ($toUpdate !== []) {
				$this->channelsManager->update($channel, Utils\ArrayHash::from($toUpdate));
			}

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			throw new Exceptions\InvalidStateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}
	}

	/**
	 * @param DevicesModuleEntities\Channels\IChannel $channel
	 * @param Utils\ArrayHash<string> $properties
	 *
	 * @return void
	 */
	private function setChannelProperties(
		DevicesModuleEntities\Channels\IChannel $channel,
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
	 * @param DevicesModuleEntities\Channels\IChannel $channel
	 * @param Utils\ArrayHash<string> $controls
	 *
	 * @return void
	 */
	private function setChannelControl(
		DevicesModuleEntities\Channels\IChannel $channel,
		Utils\ArrayHash $controls
	): void {
		$availableControls = [
			DevicesModule\Constants::CONTROL_CONFIG,
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
