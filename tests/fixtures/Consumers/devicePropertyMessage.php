<?php declare(strict_types = 1);

use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Types;
use Nette\Utils;

return [
	'messageWithoutUpdate'       => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'parent'   => null,
			'property' => 'uptime',
			'name'     => 'uptime',
		]),
		[],
	],
	'messageWithUpdate'          => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'parent'   => null,
			'property' => 'uptime',
			'name'     => 'Device uptime',
		]),
		[
			'fb.bus.node.entity.updated.device.property' => [
				'id'        => 'bbcccf8c-33ab-431b-a795-d7bb38b6b6db',
				'property'  => 'uptime',
				'name'      => 'Device uptime',
				'settable'  => false,
				'queryable' => true,
				'datatype'  => Types\DatatypeType::DATA_TYPE_INTEGER,
				'unit'      => null,
				'format'    => null,
				'device'    => 'first-device',
				'parent'    => null,
				'value'     => null,
				'expected'  => null,
				'pending'   => false,
			],
		],
	],
	'messageWithMultiRowUpdate'  => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'parent'   => null,
			'property' => 'uptime',
			'name'     => 'Device uptime',
			'datatype' => Types\DatatypeType::DATA_TYPE_STRING,
		]),
		[
			'fb.bus.node.entity.updated.device.property' => [
				'id'        => 'bbcccf8c-33ab-431b-a795-d7bb38b6b6db',
				'property'  => 'uptime',
				'name'      => 'Device uptime',
				'settable'  => false,
				'queryable' => true,
				'datatype'  => Types\DatatypeType::DATA_TYPE_STRING,
				'unit'      => null,
				'format'    => null,
				'device'    => 'first-device',
				'parent'    => null,
				'value'     => null,
				'expected'  => null,
				'pending'   => false,
			],
		],
	],
	'messageWithFormatUpdate'    => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'parent'   => null,
			'property' => 'uptime',
			'name'     => 'Device uptime',
			'datatype' => Types\DatatypeType::DATA_TYPE_INTEGER,
			'format'   => '10:50',
		]),
		[
			'fb.bus.node.entity.updated.device.property' => [
				'id'        => 'bbcccf8c-33ab-431b-a795-d7bb38b6b6db',
				'property'  => 'uptime',
				'name'      => 'Device uptime',
				'settable'  => false,
				'queryable' => true,
				'datatype'  => Types\DatatypeType::DATA_TYPE_INTEGER,
				'unit'      => null,
				'format'    => [10, 50],
				'device'    => 'first-device',
				'parent'    => null,
				'value'     => null,
				'expected'  => null,
				'pending'   => false,
			],
		],
	],
	'messageWithBadFormatUpdate' => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'parent'   => null,
			'property' => 'uptime',
			'name'     => 'Device uptime',
			'datatype' => Types\DatatypeType::DATA_TYPE_BOOLEAN,
			'format'   => '10:50',
		]),
		[
			'fb.bus.node.entity.updated.device.property' => [
				'id'        => 'bbcccf8c-33ab-431b-a795-d7bb38b6b6db',
				'property'  => 'uptime',
				'name'      => 'Device uptime',
				'settable'  => false,
				'queryable' => true,
				'datatype'  => Types\DatatypeType::DATA_TYPE_BOOLEAN,
				'unit'      => null,
				'format'    => null,
				'device'    => 'first-device',
				'parent'    => null,
				'value'     => null,
				'expected'  => null,
				'pending'   => false,
			],
		],
	],
];
