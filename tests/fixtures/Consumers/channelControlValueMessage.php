<?php declare(strict_types = 1);

use FastyBird\DevicesNode;
use Nette\Utils;

return [
	'messageWithoutUpdate' => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CHANNELS_CONTROLS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'channel' => 'channel-one',
			'control' => 'configure',
			'value'   => [
				'pulse_mode' => null,
			],
		]),
		[],
	],
	'messageWithUpdate'    => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CHANNELS_CONTROLS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'channel' => 'channel-one',
			'control' => 'configure',
			'value'   => [
				'pulse_mode' => 2,
			],
		]),
		[
			'fb.bus.node.entity.updated.device.channel.configuration' => [
				'id'      => '008d911f-e6d4-4b17-aa28-939839581cde',
				'name'    => 'pulse_mode',
				'title'   => null,
				'comment' => null,
				'default' => '0',
				'value'   => '2',
				'values'  => [
					['name' => 'disabled', 'value' => '0'],
					['name' => 'normally_off', 'value' => '1'],
					['name' => 'normally_on', 'value' => '2'],
				],
				'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
				'device'  => 'first-device',
				'channel' => 'channel-one',
			],
		],
	],
];
