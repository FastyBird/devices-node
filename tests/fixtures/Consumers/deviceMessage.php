<?php declare(strict_types = 1);

use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Types;
use Nette\Utils;

return [
	'messageWithoutUpdate'                => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device' => 'first-device',
			'parent' => null,
			'name'   => 'First device',
		]),
		[],
	],
	'messageWithUpdate'                   => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device' => 'first-device',
			'parent' => null,
			'name'   => 'Name from message bus',
		]),
		[
			'fb.bus.node.entity.updated.device' => [
				'id'         => '69786d15-fd0c-4d9f-9378-33287c2009fa',
				'identifier' => 'first-device',
				'name'       => 'Name from message bus',
				'title'      => null,
				'comment'    => null,
				'state'      => Types\DeviceConnectionState::STATE_INIT,
				'enabled'    => true,
				'control'    => ['configure'],
				'params'     => [],
				'device'     => 'first-device',
				'parent'     => null,
			],
		],
	],
	'messageWithMultiUpdate'              => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device' => 'first-device',
			'parent' => null,
			'name'   => 'Name from message bus',
			'state'  => Types\DeviceConnectionState::STATE_READY,
		]),
		[
			'fb.bus.node.entity.updated.device' => [
				'id'         => '69786d15-fd0c-4d9f-9378-33287c2009fa',
				'identifier' => 'first-device',
				'name'       => 'Name from message bus',
				'title'      => null,
				'comment'    => null,
				'state'      => Types\DeviceConnectionState::STATE_READY,
				'enabled'    => true,
				'control'    => ['configure'],
				'params'     => [],
				'device'     => 'first-device',
				'parent'     => null,
			],
		],
	],
	'messageWithMultiUpdateAndProperties' => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'     => 'first-device',
			'parent'     => null,
			'name'       => 'Name from message bus',
			'state'      => Types\DeviceConnectionState::STATE_READY,
			'properties' => ['uptime', 'rssi', 'led'],
		]),
		[
			'fb.bus.node.entity.updated.device'          => [
				'id'         => '69786d15-fd0c-4d9f-9378-33287c2009fa',
				'identifier' => 'first-device',
				'name'       => 'Name from message bus',
				'title'      => null,
				'comment'    => null,
				'state'      => Types\DeviceConnectionState::STATE_READY,
				'enabled'    => true,
				'control'    => ['configure'],
				'params'     => [],
				'device'     => 'first-device',
				'parent'     => null,
			],
			'fb.bus.node.entity.created.device.property' => [
				'property'  => 'led',
				'name'      => null,
				'settable'  => false,
				'queryable' => false,
				'datatype'  => null,
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
	'messageWithProperties'               => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'     => 'first-device',
			'parent'     => null,
			'properties' => ['uptime', 'rssi', 'led'],
		]),
		[
			'fb.bus.node.entity.created.device.property' => [
				'property'  => 'led',
				'name'      => null,
				'settable'  => false,
				'queryable' => false,
				'datatype'  => null,
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
	'messageWithMultiUpdateAndChannels'   => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'parent'   => null,
			'name'     => 'Name from message bus',
			'state'    => Types\DeviceConnectionState::STATE_READY,
			'channels' => ['channel-one', 'channel-two', 'channel-three'],
		]),
		[
			'fb.bus.node.entity.updated.device'  => [
				'id'         => '69786d15-fd0c-4d9f-9378-33287c2009fa',
				'identifier' => 'first-device',
				'name'       => 'Name from message bus',
				'title'      => null,
				'comment'    => null,
				'state'      => Types\DeviceConnectionState::STATE_READY,
				'enabled'    => true,
				'control'    => ['configure'],
				'params'     => [],
				'device'     => 'first-device',
				'parent'     => null,
			],
			'fb.bus.node.entity.created.channel' => [
				'name'    => 'channel-three',
				'title'   => null,
				'comment' => null,
				'channel' => 'channel-three',
				'control' => [],
				'params'  => [],
				'device'  => 'first-device',
				'parent'  => null,
			],
		],
	],
	'messageWithChannels'                 => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'   => 'first-device',
			'parent'   => null,
			'channels' => ['channel-one', 'channel-two', 'channel-three'],
		]),
		[
			'fb.bus.node.entity.created.channel' => [
				'name'    => 'channel-three',
				'title'   => null,
				'comment' => null,
				'channel' => 'channel-three',
				'control' => [],
				'params'  => [],
				'device'  => 'first-device',
				'parent'  => null,
			],
		],
	],
	'messageWithControls'                 => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'parent'  => null,
			'control' => ['configure', 'reset'],
		]),
		[],
	],
];
