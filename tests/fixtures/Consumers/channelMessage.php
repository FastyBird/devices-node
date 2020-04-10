<?php declare(strict_types = 1);

use FastyBird\DevicesNode;
use Nette\Utils;

return [
	'messageWithoutUpdate'                => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'channel' => 'channel-one',
			'name'    => 'Channel one',
		]),
		[],
	],
	'messageWithUpdate'                   => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'channel' => 'channel-one',
			'name'    => 'Channel updated',
		]),
		[
			'fb.bus.node.entity.updated.channel' => [
				'id'      => '17c59dfa-2edd-438e-8c49-faa4e38e5a5e',
				'name'    => 'Channel updated',
				'title'   => null,
				'comment' => null,
				'control' => ['configure'],
				'params'  => [],
				'device'  => 'first-device',
				'channel' => 'channel-one',
			],
		],
	],
	'messageWithMultiUpdate'              => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'channel' => 'channel-one',
			'name'    => 'Channel updated',
			'title'   => 'Channel title',
		]),
		[
			'fb.bus.node.entity.updated.channel' => [
				'id'      => '17c59dfa-2edd-438e-8c49-faa4e38e5a5e',
				'name'    => 'Channel updated',
				'title'   => null,
				'comment' => null,
				'control' => ['configure'],
				'params'  => [],
				'device'  => 'first-device',
				'channel' => 'channel-one',
			],
		],
	],
	'messageWithMultiUpdateAndProperties' => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'     => 'first-device',
			'channel'    => 'channel-one',
			'name'       => 'Channel updated',
			'properties' => ['switch', 'button'],
		]),
		[
			'fb.bus.node.entity.updated.channel'          => [
				'id'      => '17c59dfa-2edd-438e-8c49-faa4e38e5a5e',
				'name'    => 'Channel updated',
				'title'   => null,
				'comment' => null,
				'control' => ['configure'],
				'params'  => [],
				'device'  => 'first-device',
				'channel' => 'channel-one',
			],
			'fb.bus.node.entity.created.channel.property' => [
				'property'  => 'button',
				'name'      => null,
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
	'messageWithProperties'               => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'     => 'first-device',
			'channel'    => 'channel-one',
			'properties' => ['switch', 'button'],
		]),
		[
			'fb.bus.node.entity.created.channel.property' => [
				'property'  => 'button',
				'name'      => null,
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
	'messageWithControls'                 => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'channel' => 'channel-one',
			'control' => ['configure', 'reset'],
		]),
		[],
	],
];
