<?php declare(strict_types = 1);

use FastyBird\DevicesNode;
use Nette\Utils;

return [
	'messageWithoutUpdate'   => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_FIRMWARE_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'       => 'first-device',
			'manufacturer' => 'fastybird',
		]),
		[],
	],
	'messageWithUpdate'      => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_FIRMWARE_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device' => 'first-device',
			'name'   => 'Custom firmware',
		]),
		[
			'fb.bus.node.entity.updated.device.firmware' => [
				'id'           => '06feddf4-63e2-48bc-af13-f355f01ffbe0',
				'name'         => 'Custom firmware',
				'manufacturer' => 'fastybird',
				'version'      => null,
			],
		],
	],
	'messageWithMultiUpdate' => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_FIRMWARE_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'name'    => 'Custom firmware',
			'version' => '2.0.0',
		]),
		[
			'fb.bus.node.entity.updated.device.firmware' => [
				'id'           => '06feddf4-63e2-48bc-af13-f355f01ffbe0',
				'name'         => 'Custom firmware',
				'manufacturer' => 'fastybird',
				'version'      => '2.0.0',
			],
		],
	],
	'messageWithCreate'      => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_FIRMWARE_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'       => 'child-device',
			'manufacturer' => 'fastybird',
		]),
		[
			'fb.bus.node.entity.created.device.firmware' => [
				'name'         => null,
				'manufacturer' => 'fastybird',
				'version'      => null,
			],
		],
	],
];
