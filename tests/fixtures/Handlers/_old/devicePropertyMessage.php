<?php declare(strict_types = 1);

use FastyBird\DevicesModule\Types as DevicesModuleTypes;
use FastyBird\ModulesMetadata;
use Nette\Utils;

return [
	'messageWithoutUpdate'       => [
		ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'parent'   => null,
			'property' => 'uptime',
			'name'     => 'uptime',
		]),
		[],
	],
	'messageWithUpdate'          => [
		ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'parent'   => null,
			'property' => 'uptime',
			'name'     => 'Device uptime',
		]),
		[
			'fb.bus.entity.updated.device.property' => [
				'id'        => 'bbcccf8c-33ab-431b-a795-d7bb38b6b6db',
				'property'  => 'uptime',
				'name'      => 'Device uptime',
				'settable'  => false,
				'queryable' => true,
				'datatype'  => DevicesModuleTypes\DatatypeType::DATA_TYPE_INTEGER,
				'unit'      => null,
				'format'    => null,
				'device'    => 'first-device',
				'owner'     => '455354e8-96bd-4c29-84e7-9f10e1d4db4b',
				'parent'    => null,
				'value'     => null,
				'expected'  => null,
				'pending'   => false,
			],
		],
	],
	'messageWithMultiRowUpdate'  => [
		ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'parent'   => null,
			'property' => 'uptime',
			'name'     => 'Device uptime',
			'datatype' => DevicesModuleTypes\DatatypeType::DATA_TYPE_STRING,
		]),
		[
			'fb.bus.entity.updated.device.property' => [
				'id'        => 'bbcccf8c-33ab-431b-a795-d7bb38b6b6db',
				'property'  => 'uptime',
				'name'      => 'Device uptime',
				'settable'  => false,
				'queryable' => true,
				'datatype'  => DevicesModuleTypes\DatatypeType::DATA_TYPE_STRING,
				'unit'      => null,
				'format'    => null,
				'device'    => 'first-device',
				'owner'     => '455354e8-96bd-4c29-84e7-9f10e1d4db4b',
				'parent'    => null,
				'value'     => null,
				'expected'  => null,
				'pending'   => false,
			],
		],
	],
	'messageWithFormatUpdate'    => [
		ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'parent'   => null,
			'property' => 'uptime',
			'name'     => 'Device uptime',
			'datatype' => DevicesModuleTypes\DatatypeType::DATA_TYPE_INTEGER,
			'format'   => '10:50',
		]),
		[
			'fb.bus.entity.updated.device.property' => [
				'id'        => 'bbcccf8c-33ab-431b-a795-d7bb38b6b6db',
				'property'  => 'uptime',
				'name'      => 'Device uptime',
				'settable'  => false,
				'queryable' => true,
				'datatype'  => DevicesModuleTypes\DatatypeType::DATA_TYPE_INTEGER,
				'unit'      => null,
				'format'    => [10, 50],
				'device'    => 'first-device',
				'owner'     => '455354e8-96bd-4c29-84e7-9f10e1d4db4b',
				'parent'    => null,
				'value'     => null,
				'expected'  => null,
				'pending'   => false,
			],
		],
	],
	'messageWithBadFormatUpdate' => [
		ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'parent'   => null,
			'property' => 'uptime',
			'name'     => 'Device uptime',
			'datatype' => DevicesModuleTypes\DatatypeType::DATA_TYPE_BOOLEAN,
			'format'   => '10:50',
		]),
		[
			'fb.bus.entity.updated.device.property' => [
				'id'        => 'bbcccf8c-33ab-431b-a795-d7bb38b6b6db',
				'property'  => 'uptime',
				'name'      => 'Device uptime',
				'settable'  => false,
				'queryable' => true,
				'datatype'  => DevicesModuleTypes\DatatypeType::DATA_TYPE_BOOLEAN,
				'unit'      => null,
				'format'    => null,
				'device'    => 'first-device',
				'owner'     => '455354e8-96bd-4c29-84e7-9f10e1d4db4b',
				'parent'    => null,
				'value'     => null,
				'expected'  => null,
				'pending'   => false,
			],
		],
	],
];
