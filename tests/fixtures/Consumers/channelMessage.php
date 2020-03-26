<?php declare(strict_types = 1);

use FastyBird\DevicesNode;
use Nette\Utils;

return [
	'messageWithoutUpdate'                => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CHANNELS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'channel' => 'channel-one',
			'name'    => 'Channel one',
		]),
		[],
	],
	'messageWithUpdate'                   => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CHANNELS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'channel' => 'channel-one',
			'name'    => 'Channel updated',
		]),
		[
			'fb.bus.node.entity.updated.device.channel' => [
				'id'      => '17c59dfa-2edd-438e-8c49-faa4e38e5a5e',
				'channel' => 'channel-one',
				'name'    => 'Channel updated',
				'title'   => null,
				'comment' => null,
			],
		],
	],
	'messageWithMultiUpdate'              => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CHANNELS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'channel' => 'channel-one',
			'name'    => 'Channel updated',
			'title'   => 'Channel title',
		]),
		[
			'fb.bus.node.entity.updated.device.channel' => [
				'id'      => '17c59dfa-2edd-438e-8c49-faa4e38e5a5e',
				'channel' => 'channel-one',
				'name'    => 'Channel updated',
				'title'   => null,
				'comment' => null,
			],
		],
	],
	'messageWithMultiUpdateAndProperties' => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CHANNELS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'     => 'first-device',
			'channel'    => 'channel-one',
			'name'       => 'Channel updated',
			'properties' => ['switch', 'button'],
		]),
		[
			'fb.bus.node.entity.updated.device.channel'          => [
				'id'      => '17c59dfa-2edd-438e-8c49-faa4e38e5a5e',
				'channel' => 'channel-one',
				'name'    => 'Channel updated',
				'title'   => null,
				'comment' => null,
			],
			'fb.bus.node.entity.created.device.channel.property' => [
				'property'  => 'button',
				'name'      => null,
				'settable'  => false,
				'queryable' => false,
				'datatype'  => null,
				'unit'      => null,
				'format'    => null,
			],
		],
	],
	'messageWithProperties'               => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CHANNELS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'     => 'first-device',
			'channel'    => 'channel-one',
			'properties' => ['switch', 'button'],
		]),
		[
			'fb.bus.node.entity.created.device.channel.property' => [
				'property'  => 'button',
				'name'      => null,
				'settable'  => false,
				'queryable' => false,
				'datatype'  => null,
				'unit'      => null,
				'format'    => null,
			],
		],
	],
	'messageWithControls'                 => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CHANNELS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'channel' => 'channel-one',
			'control' => ['configure', 'reset'],
		]),
		[],
	],
];
