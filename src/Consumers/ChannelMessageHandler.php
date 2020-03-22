<?php declare(strict_types = 1);

/**
 * ChannelMessageHandler.php
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
use FastyBird\NodeLibs\Helpers as NodeLibsHelpers;
use Nette;
use Nette\Utils;
use Psr\Log;

/**
 * Channel message consumer
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelMessageHandler implements NodeLibsConsumers\IMessageHandler
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

	/** @var NodeLibsHelpers\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Channels\IChannelRepository $channelRepository,
		Models\Channels\IChannelsManager $channelsManager,
		Models\Channels\Properties\IPropertiesManager $channelPropertiesManager,
		Models\Channels\Controls\IControlsManager $channelControlManager,
		NodeLibsHelpers\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;
		$this->channelsManager = $channelsManager;
		$this->channelPropertiesManager = $channelPropertiesManager;
		$this->channelControlManager = $channelControlManager;

		$this->schemaLoader = $schemaLoader;
		$this->logger = $logger;
	}

	/**
	 * {@inheritDoc}
	 */
	public function process(
		string $routingKey,
		Utils\ArrayHash $message
	): bool {
		$findDevice = new Queries\FindPhysicalDevicesQuery();
		$findDevice->byIdentifier($message->offsetGet('device'));

		$device = $this->deviceRepository->findOneBy($findDevice, Entities\Devices\PhysicalDevice::class);

		if ($device === null) {
			$this->logger->error(sprintf('[CONSUMER] Device "%s" is not registered', $message->offsetGet('device')));

			return true;
		}

		// Check if device is in initialize mode
		if (!$this->isInitEnabled($device)) {
			$this->logger->info(sprintf('[CONSUMER] Device "%s" is in "%s" state and can\'t be updated', $message->offsetGet('device'), $device->getState()->getValue()));

			return true;
		}

		$findChannel = new Queries\FindChannelsQuery();
		$findChannel->forDevice($device);
		$findChannel->byChannel($message->offsetGet('channel'));

		$channel = $this->channelRepository->findOneBy($findChannel);

		if ($channel === null) {
			$this->logger->error(sprintf('[CONSUMER] Device channel "%s" is not registered', $message->offsetGet('device')));

			return true;
		}

		$result = true;

		switch ($routingKey) {
			case DevicesNode\Constants::RABBIT_MQ_DEVICES_CHANNELS_DATA_ROUTING_KEY:
				if ($message->offsetExists('name')) {
					$subResult = $this->setChannelName($channel, $message->offsetGet('name'));

					if (!$subResult) {
						$result = false;
					}
				}

				if ($message->offsetExists('properties')) {
					$subResult = $this->setChannelProperties($channel, $message->offsetGet('properties'));

					if (!$subResult) {
						$result = false;
					}
				}

				if ($message->offsetExists('control')) {
					$subResult = $this->setChannelControl($channel, $message->offsetGet('control'));

					if (!$subResult) {
						$result = false;
					}
				}
				break;

			default:
				throw new Exceptions\InvalidStateException('Unknown routing key');
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
			case DevicesNode\Constants::RABBIT_MQ_DEVICES_CHANNELS_DATA_ROUTING_KEY:
				return $this->schemaLoader->load('data.channel.json');

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
			return DevicesNode\Constants::RABBIT_MQ_CHANNELS_BINDINGS_ROUTING_KEY;
		}

		return [
			DevicesNode\Constants::RABBIT_MQ_DEVICES_CHANNELS_DATA_ROUTING_KEY,
		];
	}

	/**
	 * @param Entities\Channels\IChannel $channel
	 * @param string $name
	 *
	 * @return bool
	 */
	private function setChannelName(
		Entities\Channels\IChannel $channel,
		string $name
	): bool {
		$updateDevice = Utils\ArrayHash::from([
			'name' => $name,
		]);

		$this->channelsManager->update($channel, $updateDevice);

		return true;
	}

	/**
	 * @param Entities\Channels\IChannel $channel
	 * @param Utils\ArrayHash<string> $properties
	 *
	 * @return bool
	 */
	private function setChannelProperties(
		Entities\Channels\IChannel $channel,
		Utils\ArrayHash $properties
	): bool {
		foreach ($properties as $propertyName) {
			if ($channel->hasProperty($propertyName)) {
				if (in_array($propertyName, [
					Entities\Devices\Properties\IProperty::PROPERTY_IP_ADDRESS,
					Entities\Devices\Properties\IProperty::PROPERTY_STATUS_LED,
					Entities\Devices\Properties\IProperty::PROPERTY_SSID,
				], true)) {
					$this->channelPropertiesManager->create(Utils\ArrayHash::from([
						'channel'   => $channel,
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
					$this->channelPropertiesManager->create(Utils\ArrayHash::from([
						'channel'   => $channel,
						'name'      => $propertyName,
						'property'  => $propertyName,
						'settable'  => false,
						'queryable' => false,
						'datatype'  => DevicesNode\Types\DatatypeType::DATA_TYPE_INTEGER,
					]));

				} else {
					$this->channelPropertiesManager->create(Utils\ArrayHash::from([
						'channel'  => $channel,
						'property' => $propertyName,
					]));
				}
			}
		}

		// Cleanup for unused properties
		foreach ($channel->getProperties() as $property) {
			if (!in_array($property->getProperty(), (array) $properties, true)) {
				$this->channelPropertiesManager->delete($property);
			}
		}

		return true;
	}

	/**
	 * @param Entities\Channels\IChannel $channel
	 * @param Utils\ArrayHash<string> $controls
	 *
	 * @return bool
	 */
	private function setChannelControl(
		Entities\Channels\IChannel $channel,
		Utils\ArrayHash $controls
	): bool {
		$availableControls = [
			DevicesNode\Constants::CONTROL_CONFIG,
		];

		foreach ($availableControls as $availableControl) {
			if (array_search($availableControl, (array) $controls, true) !== false) {
				if (!$channel->hasControl($availableControl)) {
					$this->channelControlManager->create(Utils\ArrayHash::from([
						'channel' => $channel,
						'name'    => $availableControl,
					]));
				}

			} else {
				if ($availableControl === DevicesNode\Constants::CONTROL_CONFIG) {
					$this->channelsManager->update($channel, Utils\ArrayHash::from([
						'configuration' => [],
					]));
				}

				$control = $channel->getControl($availableControl);

				if ($control !== null) {
					$this->channelControlManager->delete($control);
				}
			}
		}

		// Cleanup for unused control
		foreach ($channel->getControls() as $control) {
			if (!in_array($control->getName(), (array) $controls, true)) {
				$this->channelControlManager->delete($control);
			}
		}

		return true;
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 *
	 * @return bool
	 */
	private function isInitEnabled(
		Entities\Devices\IDevice $device
	): bool {
		return $device->getState()->equalsValue(Types\DeviceConnectionState::STATE_INIT);
	}

}
