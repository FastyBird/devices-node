<?php declare(strict_types = 1);

/**
 * Constants.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     common
 * @since          0.1.0
 *
 * @date           18.03.20
 */

namespace FastyBird\DevicesNode;

use FastyBird\DevicesNode\Entities as DevicesNodeEntities;
use FastyBird\DevicesNode\States as DevicesNodeStates;

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
	 * Node routing
	 */

	public const ROUTE_NAME_DEVICES = 'devices';
	public const ROUTE_NAME_DEVICE = 'device';
	public const ROUTE_NAME_DEVICE_RELATIONSHIP = 'device.relationship';
	public const ROUTE_NAME_DEVICE_CHILDREN = 'device.children';
	public const ROUTE_NAME_DEVICE_PROPERTIES = 'device.properties';
	public const ROUTE_NAME_DEVICE_PROPERTY = 'device.property';
	public const ROUTE_NAME_DEVICE_PROPERTY_RELATIONSHIP = 'device.property.relationship';
	public const ROUTE_NAME_DEVICE_CONFIGURATION_ROWS = 'device.configuration.rows';
	public const ROUTE_NAME_DEVICE_CONFIGURATION_ROW = 'device.configuration.row';
	public const ROUTE_NAME_DEVICE_CONFIGURATION_ROW_RELATIONSHIP = 'device.configuration.row.relationship';
	public const ROUTE_NAME_DEVICE_HARDWARE = 'device.hardware';
	public const ROUTE_NAME_DEVICE_HARDWARE_RELATIONSHIP = 'device.hardware.relationship';
	public const ROUTE_NAME_DEVICE_FIRMWARE = 'device.firmware';
	public const ROUTE_NAME_DEVICE_FIRMWARE_RELATIONSHIP = 'device.firmware.relationship';
	public const ROUTE_NAME_CHANNELS = 'channels';
	public const ROUTE_NAME_CHANNEL = 'channel';
	public const ROUTE_NAME_CHANNEL_RELATIONSHIP = 'channel.relationship';
	public const ROUTE_NAME_CHANNEL_PROPERTIES = 'channel.properties';
	public const ROUTE_NAME_CHANNEL_PROPERTY = 'channel.property';
	public const ROUTE_NAME_CHANNEL_PROPERTY_RELATIONSHIP = 'channel.property.relationship';
	public const ROUTE_NAME_CHANNEL_CONFIGURATION_ROWS = 'channel.configuration.rows';
	public const ROUTE_NAME_CHANNEL_CONFIGURATION_ROW = 'channel.configuration.row';
	public const ROUTE_NAME_CHANNEL_CONFIGURATION_ROW_RELATIONSHIP = 'channel.configuration.row.relationship';

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

		DevicesNodeStates\Devices\Property::class  => 'fb.bus.node.entity.[ACTION].device.property',
		DevicesNodeStates\Channels\Property::class => 'fb.bus.node.entity.[ACTION].channel.property',
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
	 * Message bus origins
	 */

	public const NODE_MQTT_ORIGIN = 'com.fastybird.mqtt-node';
	public const NODE_WEBSOCKETS_ORIGIN = 'com.fastybird.websockets-node';
	public const NODE_TRIGGERS_ORIGIN = 'com.fastybird.triggers-node';

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
