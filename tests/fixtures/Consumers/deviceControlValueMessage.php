<?php declare(strict_types = 1);

use FastyBird\DevicesNode;
use Nette\Utils;

return [
	'messageWithoutUpdate' => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CONTROLS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'parent'  => null,
			'control' => 'configure',
			'value'   => [
				'sensor_expected_power' => null,
			],
		]),
		[],
	],
	'messageWithUpdate'    => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CONTROLS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'parent'  => null,
			'control' => 'configure',
			'value'   => [
				'sensor_expected_power' => 300,
			],
		]),
		[
			'fb.bus.node.entity.updated.device.configuration' => [
				'id'      => '138c6cfc-ed49-476b-9f1e-6ee1dcb24f0b',
				'name'    => 'sensor_expected_power',
				'title'   => null,
				'comment' => null,
				'default' => null,
				'value'   => 300.0,
				'min'     => 0.0,
				'max'     => 500.0,
				'step'    => 1.0,
				'type'    => 'number',
				'device'  => 'first-device',
				'parent'  => null,
			],
		],
	],
];
