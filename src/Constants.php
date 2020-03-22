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
	 * Message bus routing key for devices properties messages
	 */

	// Devices
	public const RABBIT_MQ_DEVICES_DATA_ROUTING_KEY = 'fb.bus.node.data.device';
	public const RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY = 'fb.bus.node.data.device.property';
	public const RABBIT_MQ_DEVICES_CONTROLS_DATA_ROUTING_KEY = 'fb.bus.node.data.device.control';
	public const RABBIT_MQ_DEVICES_HARDWARE_DATA_ROUTING_KEY = 'fb.bus.node.data.device.hardware';
	public const RABBIT_MQ_DEVICES_FIRMWARE_DATA_ROUTING_KEY = 'fb.bus.node.data.device.firmware';

	// Channels
	public const RABBIT_MQ_DEVICES_CHANNELS_DATA_ROUTING_KEY = 'fb.bus.node.data.device.channel';
	public const RABBIT_MQ_DEVICES_CHANNELS_PROPERTIES_DATA_ROUTING_KEY = 'fb.bus.node.data.device.channel.property';
	public const RABBIT_MQ_DEVICES_CHANNELS_CONTROLS_DATA_ROUTING_KEY = 'fb.bus.node.data.device.channel.control';

	public const RABBIT_MQ_DEVICES_BINDINGS_ROUTING_KEYS = [
		'fb.bus.node.data.device',              // Data
	];

	public const RABBIT_MQ_DEVICES_PARTS_BINDINGS_ROUTING_KEYS = [
		'fb.bus.node.data.device.*',            // Data
	];

	public const RABBIT_MQ_CHANNELS_BINDINGS_ROUTING_KEY = [
		'fb.bus.node.data.device.channel',      // Data
	];

	public const RABBIT_MQ_CHANNELS_PARTS_BINDINGS_ROUTING_KEY = [
		'fb.bus.node.data.device.channel.*',    // Data
	];

	/**
	 * Control actions
	 */
	public const CONTROL_CONFIG = 'configure';
	public const CONTROL_RESET = 'reset';
	public const CONTROL_RECONNECT = 'reconnect';
	public const CONTROL_FACTORY_RESET = 'factory-reset';
	public const CONTROL_OTA = 'ota';

}
