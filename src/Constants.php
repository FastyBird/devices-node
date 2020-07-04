<?php declare(strict_types = 1);

/**
 * Constants.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     common
 * @since          0.1.0
 *
 * @date           18.03.20
 */

namespace FastyBird\DevicesNode;

use FastyBird\DevicesNode\Entities as DevicesNodeEntities;

/**
 * Service constants
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     common
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class Constants
{

	/**
	 * Message bus routing keys mapping
	 */
	public const RABBIT_MQ_ENTITIES_ROUTING_KEYS_MAPPING = [
		DevicesNodeEntities\Devices\Device::class                  => 'fb.bus.node.entity.[ACTION].device',
		DevicesNodeEntities\Devices\Properties\Property::class     => 'fb.bus.node.entity.[ACTION].device.property',
		DevicesNodeEntities\Devices\Configuration\Row::class       => 'fb.bus.node.entity.[ACTION].device.configuration',
		DevicesNodeEntities\Devices\PhysicalDevice\Hardware::class => 'fb.bus.node.entity.[ACTION].device.hardware',
		DevicesNodeEntities\Devices\PhysicalDevice\Firmware::class => 'fb.bus.node.entity.[ACTION].device.firmware',
		DevicesNodeEntities\Channels\Channel::class                => 'fb.bus.node.entity.[ACTION].channel',
		DevicesNodeEntities\Channels\Properties\Property::class    => 'fb.bus.node.entity.[ACTION].channel.property',
		DevicesNodeEntities\Channels\Configuration\Row::class      => 'fb.bus.node.entity.[ACTION].channel.configuration',
	];

	public const RABBIT_MQ_ENTITIES_ROUTING_KEY_ACTION_REPLACE_STRING = '[ACTION]';

	/**
	 * Message bus routing key for devices & channels properties messages
	 */

	// Devices
	public const RABBIT_MQ_DEVICES_DATA_ROUTING_KEY = 'fb.bus.node.data.device';
	public const RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY = 'fb.bus.node.data.device.property';
	public const RABBIT_MQ_DEVICES_CONTROLS_DATA_ROUTING_KEY = 'fb.bus.node.data.device.control';
	public const RABBIT_MQ_DEVICES_HARDWARE_DATA_ROUTING_KEY = 'fb.bus.node.data.device.hardware';
	public const RABBIT_MQ_DEVICES_FIRMWARE_DATA_ROUTING_KEY = 'fb.bus.node.data.device.firmware';

	// Channels
	public const RABBIT_MQ_CHANNELS_DATA_ROUTING_KEY = 'fb.bus.node.data.channel';
	public const RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY = 'fb.bus.node.data.channel.property';
	public const RABBIT_MQ_CHANNELS_CONTROLS_DATA_ROUTING_KEY = 'fb.bus.node.data.channel.control';

	/**
	 * Microservices origins
	 */

	public const NODE_MQTT_ORIGIN = 'com.fastybird.mqtt-node';

	/**
	 * Data types
	 */
	public const DATA_TYPE_BOOLEAN = 'boolean';
	public const DATA_TYPE_NUMBER = 'number';
	public const DATA_TYPE_SELECT = 'select';
	public const DATA_TYPE_TEXT = 'text';

	/**
	 * Control actions
	 */
	public const CONTROL_CONFIG = 'configure';
	public const CONTROL_RESET = 'reset';
	public const CONTROL_RECONNECT = 'reconnect';
	public const CONTROL_FACTORY_RESET = 'factory-reset';
	public const CONTROL_OTA = 'ota';

}
