<?php declare(strict_types = 1);

/**
 * DeviceMessageHandler.php
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
use FastyBird\DevicesModule\Types as DevicesModuleTypes;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\MqttPlugin\Entities as MqttPluginEntities;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Device attributes MQTT message handler
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Handlers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DeviceMessageHandler
{

	use Nette\SmartObject;

	/** @var DevicesModuleModels\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var DevicesModuleModels\Devices\IDevicesManager */
	private $devicesManager;

	/** @var DevicesModuleModels\Devices\Properties\IPropertiesManager */
	private $devicePropertiesManager;

	/** @var DevicesModuleModels\Devices\Controls\IControlsManager */
	private $deviceControlManager;

	/** @var DevicesModuleModels\Channels\IChannelRepository */
	private $channelRepository;

	/** @var DevicesModuleModels\Channels\IChannelsManager */
	private $channelsManager;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		DevicesModuleModels\Devices\IDeviceRepository $deviceRepository,
		DevicesModuleModels\Devices\IDevicesManager $devicesManager,
		DevicesModuleModels\Devices\Properties\IPropertiesManager $devicePropertiesManager,
		DevicesModuleModels\Devices\Controls\IControlsManager $deviceControlManager,
		DevicesModuleModels\Channels\IChannelRepository $channelRepository,
		DevicesModuleModels\Channels\IChannelsManager $channelsManager,
		Common\Persistence\ManagerRegistry $managerRegistry,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->devicesManager = $devicesManager;
		$this->devicePropertiesManager = $devicePropertiesManager;
		$this->deviceControlManager = $deviceControlManager;
		$this->channelRepository = $channelRepository;
		$this->channelsManager = $channelsManager;

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
		MqttPluginEntities\DeviceAttribute $entity
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
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$toUpdate = [];

			if ($entity->getParent() !== null) {
				$findQuery = new DevicesModuleQueries\FindDevicesQuery();
				$findQuery->byIdentifier($entity->getParent());

				$parent = $this->deviceRepository->findOneBy($findQuery);

				if ($parent !== null) {
					$toUpdate['parent'] = $parent;
				}
			}

			if ($entity->getAttribute() === MqttPluginEntities\Attribute::NAME) {
				$toUpdate['name'] = $entity->getValue();
			}

			if (
				$entity->getAttribute() === MqttPluginEntities\Attribute::STATE &&
				DevicesModuleTypes\DeviceConnectionState::isValidValue($entity->getValue())
			) {
				$toUpdate['state'] = $entity->getValue();
			}

			if ($entity->getAttribute() === MqttPluginEntities\Attribute::PROPERTIES && is_array($entity->getValue())) {
				$this->setDeviceProperties($device, Utils\ArrayHash::from($entity->getValue()));
			}

			if ($entity->getAttribute() === MqttPluginEntities\Attribute::CHANNELS && is_array($entity->getValue())) {
				$this->setDeviceChannels($device, Utils\ArrayHash::from($entity->getValue()));
			}

			if ($entity->getAttribute() === MqttPluginEntities\Attribute::CONTROL && is_array($entity->getValue())) {
				$this->setDeviceControl($device, Utils\ArrayHash::from($entity->getValue()));
			}

			if ($toUpdate !== []) {
				$this->devicesManager->update($device, Utils\ArrayHash::from($toUpdate));
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

	/**
	 * @param DevicesModuleEntities\Devices\IDevice $device
	 * @param Utils\ArrayHash<string> $properties
	 *
	 * @return void
	 */
	private function setDeviceProperties(
		DevicesModuleEntities\Devices\IDevice $device,
		Utils\ArrayHash $properties
	): void {
		foreach ($properties as $propertyName) {
			if (!$device->hasProperty($propertyName)) {
				if (in_array($propertyName, [
					DevicesModuleEntities\Devices\Properties\IProperty::PROPERTY_IP_ADDRESS,
					DevicesModuleEntities\Devices\Properties\IProperty::PROPERTY_STATUS_LED,
					DevicesModuleEntities\Devices\Properties\IProperty::PROPERTY_SSID,
				], true)) {
					$this->devicePropertiesManager->create(Utils\ArrayHash::from([
						'device'    => $device,
						'name'      => $propertyName,
						'property'  => $propertyName,
						'settable'  => false,
						'queryable' => false,
						'datatype'  => DevicesModuleTypes\DatatypeType::DATA_TYPE_STRING,
					]));

				} elseif (in_array($propertyName, [
					DevicesModuleEntities\Devices\Properties\IProperty::PROPERTY_INTERVAL,
					DevicesModuleEntities\Devices\Properties\IProperty::PROPERTY_UPTIME,
					DevicesModuleEntities\Devices\Properties\IProperty::PROPERTY_FREE_HEAP,
					DevicesModuleEntities\Devices\Properties\IProperty::PROPERTY_CPU_LOAD,
					DevicesModuleEntities\Devices\Properties\IProperty::PROPERTY_VCC,
					DevicesModuleEntities\Devices\Properties\IProperty::PROPERTY_RSSI,
				], true)) {
					$this->devicePropertiesManager->create(Utils\ArrayHash::from([
						'device'    => $device,
						'name'      => $propertyName,
						'property'  => $propertyName,
						'settable'  => false,
						'queryable' => false,
						'datatype'  => DevicesModuleTypes\DatatypeType::DATA_TYPE_INTEGER,
					]));

				} else {
					$this->devicePropertiesManager->create(Utils\ArrayHash::from([
						'device'   => $device,
						'property' => $propertyName,
					]));
				}
			}
		}

		// Cleanup for unused properties
		foreach ($device->getProperties() as $property) {
			if (!in_array($property->getProperty(), (array) $properties, true)) {
				$this->devicePropertiesManager->delete($property);
			}
		}
	}

	/**
	 * @param DevicesModuleEntities\Devices\IDevice $device
	 * @param Utils\ArrayHash<string> $channels
	 *
	 * @return void
	 */
	private function setDeviceChannels(
		DevicesModuleEntities\Devices\IDevice $device,
		Utils\ArrayHash $channels
	): void {
		foreach ($channels as $channelId) {
			$findQuery = new DevicesModuleQueries\FindChannelsQuery();
			$findQuery->forDevice($device);
			$findQuery->byChannel($channelId);

			// Check if channel exists
			$channel = $this->channelRepository->findOneBy($findQuery);

			// ...if not, create it
			if ($channel === null) {
				$this->channelsManager->create(Utils\ArrayHash::from([
					'channel' => $channelId,
					'device'  => $device,
				]));
			}
		}

		// Cleanup for unused channels
		foreach ($device->getChannels() as $channel) {
			if (!in_array($channel->getChannel(), (array) $channels, true)) {
				$this->channelsManager->delete($channel);
			}
		}
	}

	/**
	 * @param DevicesModuleEntities\Devices\IDevice $device
	 * @param Utils\ArrayHash<string> $controls
	 *
	 * @return void
	 */
	private function setDeviceControl(
		DevicesModuleEntities\Devices\IDevice $device,
		Utils\ArrayHash $controls
	): void {
		$availableControls = [
			DevicesModule\Constants::CONTROL_CONFIG,
			DevicesModule\Constants::CONTROL_RESET,
			DevicesModule\Constants::CONTROL_RECONNECT,
			DevicesModule\Constants::CONTROL_FACTORY_RESET,
			DevicesModule\Constants::CONTROL_OTA,
		];

		foreach ($controls as $controlName) {
			if (in_array($controlName, $availableControls, true)) {
				if (!$device->hasControl($controlName)) {
					$this->deviceControlManager->create(Utils\ArrayHash::from([
						'device' => $device,
						'name'   => $controlName,
					]));
				}
			}
		}

		// Cleanup for unused control
		foreach ($device->getControls() as $control) {
			if (!in_array($control->getName(), (array) $controls, true)) {
				$this->deviceControlManager->delete($control);
			}
		}
	}

}
