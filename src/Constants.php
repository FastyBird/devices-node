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

use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\ModulesMetadata;

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
	public const MESSAGE_BUS_CREATED_ENTITIES_ROUTING_KEYS_MAPPING = [
		DevicesModuleEntities\Devices\Device::class               => ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_CREATED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Devices\Properties\Property::class  => ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_PROPERTY_CREATED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Devices\Configuration\Row::class    => ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_CONFIGURATION_CREATED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Channels\Channel::class             => ModulesMetadata\Constants::MESSAGE_BUS_CHANNELS_CREATED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Channels\Properties\Property::class => ModulesMetadata\Constants::MESSAGE_BUS_CHANNELS_PROPERTY_CREATED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Channels\Configuration\Row::class   => ModulesMetadata\Constants::MESSAGE_BUS_CHANNELS_CONFIGURATION_CREATED_ENTITY_ROUTING_KEY,
	];

	public const MESSAGE_BUS_UPDATED_ENTITIES_ROUTING_KEYS_MAPPING = [
		DevicesModuleEntities\Devices\Device::class               => ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_UPDATED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Devices\Properties\Property::class  => ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_PROPERTY_UPDATED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Devices\Configuration\Row::class    => ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_CONFIGURATION_UPDATED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Channels\Channel::class             => ModulesMetadata\Constants::MESSAGE_BUS_CHANNELS_UPDATED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Channels\Properties\Property::class => ModulesMetadata\Constants::MESSAGE_BUS_CHANNELS_PROPERTY_UPDATED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Channels\Configuration\Row::class   => ModulesMetadata\Constants::MESSAGE_BUS_CHANNELS_CONFIGURATION_UPDATED_ENTITY_ROUTING_KEY,
	];

	public const MESSAGE_BUS_DELETED_ENTITIES_ROUTING_KEYS_MAPPING = [
		DevicesModuleEntities\Devices\Device::class               => ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_DELETED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Devices\Properties\Property::class  => ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_PROPERTY_DELETED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Devices\Configuration\Row::class    => ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_CONFIGURATION_DELETED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Channels\Channel::class             => ModulesMetadata\Constants::MESSAGE_BUS_CHANNELS_DELETED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Channels\Properties\Property::class => ModulesMetadata\Constants::MESSAGE_BUS_CHANNELS_PROPERTY_DELETED_ENTITY_ROUTING_KEY,
		DevicesModuleEntities\Channels\Configuration\Row::class   => ModulesMetadata\Constants::MESSAGE_BUS_CHANNELS_CONFIGURATION_DELETED_ENTITY_ROUTING_KEY,
	];

}
