<?php declare(strict_types = 1);

use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Types;
use Nette\Utils;

return [
	'messageWithoutUpdate'    => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'channel'  => 'channel-one',
			'property' => 'switch',
			'name'     => 'switch',
		]),
		[],
	],
	'messageWithUpdate'       => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'channel'  => 'channel-one',
			'property' => 'switch',
			'name'     => 'Relay switch',
		]),
		[
			'fb.bus.node.entity.updated.channel.property' => [
				'id'        => 'bbcccf8c-33ab-431b-a795-d7bb38b6b6db',
				'property'  => 'switch',
				'name'      => 'Relay switch',
				'settable'  => true,
				'queryable' => true,
				'datatype'  => 'enum',
				'unit'      => null,
				'format'    => ['on', 'off', 'toggle'],
				'device'    => 'first-device',
				'channel'   => 'channel-one',
			],
		],
	],
	'messageWithCreate'       => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'channel'  => 'channel-one',
			'property' => 'button',
			'name'     => 'Device button',
		]),
		[
			'fb.bus.node.entity.created.channel.property' => [
				'property'  => 'button',
				'name'      => 'Device button',
				'settable'  => false,
				'queryable' => false,
				'datatype'  => null,
				'unit'      => null,
				'format'    => null,
				'device'    => 'first-device',
				'channel'   => 'channel-one',
			],
		],
	],
	'messageWithSecondUpdate' => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'channel'  => 'channel-one',
			'property' => 'switch',
			'name'     => 'Relay switch',
			'datatype' => Types\DatatypeType::DATA_TYPE_STRING,
		]),
		[
			'fb.bus.node.entity.updated.channel.property' => [
				'id'        => 'bbcccf8c-33ab-431b-a795-d7bb38b6b6db',
				'property'  => 'switch',
				'name'      => 'Relay switch',
				'settable'  => true,
				'queryable' => true,
				'datatype'  => 'string',
				'unit'      => null,
				'format'    => null,
				'device'    => 'first-device',
				'channel'   => 'channel-one',
			],
		],
	],
	'messageWithFormatUpdate' => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'channel'  => 'channel-one',
			'property' => 'switch',
			'name'     => 'Relay switch',
			'datatype' => Types\DatatypeType::DATA_TYPE_INTEGER,
			'format'   => 'on,off',
		]),
		[
			'fb.bus.node.entity.updated.channel.property' => [
				'id'        => 'bbcccf8c-33ab-431b-a795-d7bb38b6b6db',
				'property'  => 'switch',
				'name'      => 'Relay switch',
				'settable'  => true,
				'queryable' => true,
				'datatype'  => 'integer',
				'unit'      => null,
				'format'    => null,
				'device'    => 'first-device',
				'channel'   => 'channel-one',
			],
		],
	],
];
