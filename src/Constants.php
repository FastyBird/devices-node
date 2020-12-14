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

use FastyBird\DevicesModule\Entities as DevicesModuleEntities;

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
		DevicesModuleEntities\Devices\Device::class                  => 'fb.bus.node.entity.[ACTION].device',
		DevicesModuleEntities\Devices\Properties\Property::class     => 'fb.bus.node.entity.[ACTION].device.property',
		DevicesModuleEntities\Devices\Configuration\Row::class       => 'fb.bus.node.entity.[ACTION].device.configuration',
		DevicesModuleEntities\Devices\PhysicalDevice\Hardware::class => 'fb.bus.node.entity.[ACTION].device.hardware',
		DevicesModuleEntities\Devices\PhysicalDevice\Firmware::class => 'fb.bus.node.entity.[ACTION].device.firmware',
		DevicesModuleEntities\Channels\Channel::class                => 'fb.bus.node.entity.[ACTION].channel',
		DevicesModuleEntities\Channels\Properties\Property::class    => 'fb.bus.node.entity.[ACTION].channel.property',
		DevicesModuleEntities\Channels\Configuration\Row::class      => 'fb.bus.node.entity.[ACTION].channel.configuration',
	];

	public const RABBIT_MQ_ENTITIES_ROUTING_KEY_ACTION_REPLACE_STRING = '[ACTION]';

	/**
	 * Message bus routing key for devices & channels properties messages
	 */

	// Devices
	public const RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY = 'fb.bus.node.data.device.property';
	public const RABBIT_MQ_DEVICES_CONTROLS_DATA_ROUTING_KEY = 'fb.bus.node.data.device.control';

	// Channels
	public const RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY = 'fb.bus.node.data.channel.property';
	public const RABBIT_MQ_CHANNELS_CONTROLS_DATA_ROUTING_KEY = 'fb.bus.node.data.channel.control';

	/**
	 * Message bus origins
	 */

	public const NODE_UI_ORIGIN = 'com.fastybird.ui-module';
	public const NODE_TRIGGERS_ORIGIN = 'com.fastybird.triggers-module';

}
