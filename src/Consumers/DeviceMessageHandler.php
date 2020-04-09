<?php declare(strict_types = 1);

/**
 * DeviceMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 * @since          0.1.0
 *
 * @date           18.03.20
 */

namespace FastyBird\DevicesNode\Consumers;

use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Queries;
use FastyBird\DevicesNode\Types;
use FastyBird\NodeLibs\Consumers as NodeLibsConsumers;
use FastyBird\NodeLibs\Exceptions as NodeLibsExceptions;
use FastyBird\NodeLibs\Helpers as NodeLibsHelpers;
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
final class DeviceMessageHandler implements NodeLibsConsumers\IMessageHandler
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

	/** @var NodeLibsHelpers\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Devices\IDevicesManager $devicesManager,
		Models\Devices\Properties\IPropertiesManager $devicePropertiesManager,
		Models\Devices\Controls\IControlsManager $deviceControlManager,
		Models\Channels\IChannelRepository $channelRepository,
		Models\Channels\IChannelsManager $channelsManager,
		NodeLibsHelpers\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger
	) {
		$this->deviceRepository = $deviceRepository;
		$this->devicesManager = $devicesManager;
		$this->devicePropertiesManager = $devicePropertiesManager;
		$this->deviceControlManager = $deviceControlManager;
		$this->channelRepository = $channelRepository;
		$this->channelsManager = $channelsManager;

		$this->schemaLoader = $schemaLoader;
		$this->logger = $logger;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws NodeLibsExceptions\TerminateException
	 *
	 * @throws NodeLibsExceptions\TerminateException
	 */
	public function process(
		string $routingKey,
		Utils\ArrayHash $message
	): bool {
		$findQuery = new Queries\FindDevicesQuery();
		$findQuery->byIdentifier($message->offsetGet('device'));

		$device = $this->deviceRepository->findOneBy($findQuery);

		if ($device === null) {
			$this->logger->error(sprintf('[CONSUMER] Device "%s" is not registered', $message->offsetGet('device')));

			return true;
		}

		$result = true;

		try {
			switch ($routingKey) {
				case DevicesNode\Constants::RABBIT_MQ_DEVICES_DATA_ROUTING_KEY:
					$toUpdate = [];

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
						$subResult = $this->setDeviceProperties($device, $message->offsetGet('properties'));

						if (!$subResult) {
							$result = false;
						}
					}

					if ($message->offsetExists('channels')) {
						$subResult = $this->setDeviceChannels($device, $message->offsetGet('channels'));

						if (!$subResult) {
							$result = false;
						}
					}

					if ($message->offsetExists('control')) {
						$subResult = $this->setDeviceControl($device, $message->offsetGet('control'));

						if (!$subResult) {
							$result = false;
						}
					}

					if ($toUpdate !== []) {
						$this->devicesManager->update($device, Utils\ArrayHash::from($toUpdate));
					}
					break;

				default:
					throw new Exceptions\InvalidStateException('Unknown routing key');
			}

		} catch (Throwable $ex) {
			throw new NodeLibsExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($result) {
			$this->logger->info('[CONSUMER] Successfully consumed entity message');
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSchema(string $routingKey): string
	{
		switch ($routingKey) {
			case DevicesNode\Constants::RABBIT_MQ_DEVICES_DATA_ROUTING_KEY:
				return $this->schemaLoader->load('data.device.json');

			default:
				throw new Exceptions\InvalidStateException('Unknown routing key');
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRoutingKeys(bool $binding = false): array
	{
		if ($binding) {
			return DevicesNode\Constants::RABBIT_MQ_DEVICES_BINDINGS_ROUTING_KEYS;
		}

		return [
			DevicesNode\Constants::RABBIT_MQ_DEVICES_DATA_ROUTING_KEY,
		];
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param Utils\ArrayHash<string> $properties
	 *
	 * @return bool
	 */
	private function setDeviceProperties(
		Entities\Devices\IDevice $device,
		Utils\ArrayHash $properties
	): bool {
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

		return true;
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param Utils\ArrayHash<string> $channels
	 *
	 * @return bool
	 */
	private function setDeviceChannels(
		Entities\Devices\IDevice $device,
		Utils\ArrayHash $channels
	): bool {
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

		return true;
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param Utils\ArrayHash<string> $controls
	 *
	 * @return bool
	 */
	private function setDeviceControl(
		Entities\Devices\IDevice $device,
		Utils\ArrayHash $controls
	): bool {
		$availableControls = [
			DevicesNode\Constants::CONTROL_CONFIG,
			DevicesNode\Constants::CONTROL_RESET,
			DevicesNode\Constants::CONTROL_RECONNECT,
			DevicesNode\Constants::CONTROL_FACTORY_RESET,
			DevicesNode\Constants::CONTROL_OTA,
		];

		foreach ($availableControls as $availableControl) {
			if (array_search($availableControl, (array) $controls, true) !== false) {
				if (!$device->hasControl($availableControl)) {
					$this->deviceControlManager->create(Utils\ArrayHash::from([
						'device' => $device,
						'name'   => $availableControl,
					]));
				}

			} else {
				if ($availableControl === DevicesNode\Constants::CONTROL_CONFIG) {
					$this->devicesManager->update($device, Utils\ArrayHash::from([
						'configuration' => [],
					]));
				}

				$control = $device->getControl($availableControl);

				if ($control !== null) {
					$this->deviceControlManager->delete($control);
				}
			}
		}

		// Cleanup for unused control
		foreach ($device->getControls() as $control) {
			if (!in_array($control->getName(), (array) $controls, true)) {
				$this->deviceControlManager->delete($control);
			}
		}

		return true;
	}

}
