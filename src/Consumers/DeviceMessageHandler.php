<?php declare(strict_types = 1);

/**
 * DeviceMessageHandler.php
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
use FastyBird\DevicesNode\Types;
use FastyBird\NodeExchange\Consumers as NodeExchangeConsumers;
use FastyBird\NodeExchange\Exceptions as NodeExchangeExceptions;
use FastyBird\NodeMetadata;
use FastyBird\NodeMetadata\Loaders as NodeMetadataLoaders;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Device message consumer
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DeviceMessageHandler implements NodeExchangeConsumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var Models\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var Models\Devices\IDevicesManager */
	private $devicesManager;

	/** @var Models\Devices\Properties\IPropertiesManager */
	private $devicePropertiesManager;

	/** @var Models\Devices\Controls\IControlsManager */
	private $deviceControlManager;

	/** @var Models\Channels\IChannelRepository */
	private $channelRepository;

	/** @var Models\Channels\IChannelsManager */
	private $channelsManager;

	/** @var NodeMetadataLoaders\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Devices\IDevicesManager $devicesManager,
		Models\Devices\Properties\IPropertiesManager $devicePropertiesManager,
		Models\Devices\Controls\IControlsManager $deviceControlManager,
		Models\Channels\IChannelRepository $channelRepository,
		Models\Channels\IChannelsManager $channelsManager,
		NodeMetadataLoaders\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger,
		Common\Persistence\ManagerRegistry $managerRegistry
	) {
		$this->deviceRepository = $deviceRepository;
		$this->devicesManager = $devicesManager;
		$this->devicePropertiesManager = $devicePropertiesManager;
		$this->deviceControlManager = $deviceControlManager;
		$this->channelRepository = $channelRepository;
		$this->channelsManager = $channelsManager;

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
		string $origin,
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
			switch ($routingKey) {
				case DevicesNode\Constants::RABBIT_MQ_DEVICES_DATA_ROUTING_KEY:
					// Start transaction connection to the database
					$this->getOrmConnection()->beginTransaction();

					$toUpdate = [];

					if ($message->offsetExists('parent') && $message->offsetGet('parent') !== null) {
						$findQuery = new Queries\FindDevicesQuery();
						$findQuery->byIdentifier($message->offsetGet('parent'));

						$parent = $this->deviceRepository->findOneBy($findQuery);

						if ($parent !== null) {
							$toUpdate['parent'] = $parent;
						}
					}

					if ($message->offsetExists('name')) {
						$toUpdate['name'] = $message->offsetGet('name');
					}

					if (
						$message->offsetExists('state') &&
						Types\DeviceConnectionState::isValidValue($message->offsetGet('state'))
					) {
						$toUpdate['state'] = $message->offsetGet('state');
					}

					if ($message->offsetExists('properties')) {
						$this->setDeviceProperties($device, $message->offsetGet('properties'));
					}

					if ($message->offsetExists('channels')) {
						$this->setDeviceChannels($device, $message->offsetGet('channels'));
					}

					if ($message->offsetExists('control')) {
						$this->setDeviceControl($device, $message->offsetGet('control'));
					}

					if ($toUpdate !== []) {
						$this->devicesManager->update($device, Utils\ArrayHash::from($toUpdate));
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

		$this->logger->info('[CONSUMER] Successfully consumed entity message', [
			'message' => [
				'routingKey' => $routingKey,
				'origin'     => $origin,
			],
		]);

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSchema(string $routingKey, string $origin): ?string
	{
		if ($origin === DevicesNode\Constants::NODE_MQTT_ORIGIN) {
			switch ($routingKey) {
				case DevicesNode\Constants::RABBIT_MQ_DEVICES_DATA_ROUTING_KEY:
					return $this->schemaLoader->load(NodeMetadata\Constants::RESOURCES_FOLDER . '/schemas/mqtt-node/data.device.json');
			}
		}

		return null;
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param Utils\ArrayHash<string> $properties
	 *
	 * @return void
	 */
	private function setDeviceProperties(
		Entities\Devices\IDevice $device,
		Utils\ArrayHash $properties
	): void {
		foreach ($properties as $propertyName) {
			if (!$device->hasProperty($propertyName)) {
				if (in_array($propertyName, [
					Entities\Devices\Properties\IProperty::PROPERTY_IP_ADDRESS,
					Entities\Devices\Properties\IProperty::PROPERTY_STATUS_LED,
					Entities\Devices\Properties\IProperty::PROPERTY_SSID,
				], true)) {
					$this->devicePropertiesManager->create(Utils\ArrayHash::from([
						'device'    => $device,
						'name'      => $propertyName,
						'property'  => $propertyName,
						'settable'  => false,
						'queryable' => false,
						'datatype'  => DevicesNode\Types\DatatypeType::DATA_TYPE_STRING,
					]));

				} elseif (in_array($propertyName, [
					Entities\Devices\Properties\IProperty::PROPERTY_INTERVAL,
					Entities\Devices\Properties\IProperty::PROPERTY_UPTIME,
					Entities\Devices\Properties\IProperty::PROPERTY_FREE_HEAP,
					Entities\Devices\Properties\IProperty::PROPERTY_CPU_LOAD,
					Entities\Devices\Properties\IProperty::PROPERTY_VCC,
					Entities\Devices\Properties\IProperty::PROPERTY_RSSI,
				], true)) {
					$this->devicePropertiesManager->create(Utils\ArrayHash::from([
						'device'    => $device,
						'name'      => $propertyName,
						'property'  => $propertyName,
						'settable'  => false,
						'queryable' => false,
						'datatype'  => DevicesNode\Types\DatatypeType::DATA_TYPE_INTEGER,
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
	 * @param Entities\Devices\IDevice $device
	 * @param Utils\ArrayHash<string> $channels
	 *
	 * @return void
	 */
	private function setDeviceChannels(
		Entities\Devices\IDevice $device,
		Utils\ArrayHash $channels
	): void {
		foreach ($channels as $channelId) {
			$findQuery = new Queries\FindChannelsQuery();
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
	 * @param Entities\Devices\IDevice $device
	 * @param Utils\ArrayHash<string> $controls
	 *
	 * @return void
	 */
	private function setDeviceControl(
		Entities\Devices\IDevice $device,
		Utils\ArrayHash $controls
	): void {
		$availableControls = [
			DevicesNode\Constants::CONTROL_CONFIG,
			DevicesNode\Constants::CONTROL_RESET,
			DevicesNode\Constants::CONTROL_RECONNECT,
			DevicesNode\Constants::CONTROL_FACTORY_RESET,
			DevicesNode\Constants::CONTROL_OTA,
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
