<?php declare(strict_types = 1);

use FastyBird\DevicesNode;
use Nette\Utils;

return [
	'messageWithoutUpdate'      => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_HARDWARE_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'       => 'first-device',
			'parent'       => null,
			'manufacturer' => 'itead',
		]),
		[],
	],
	'messageWithUpdate'         => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_HARDWARE_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'       => 'first-device',
			'parent'       => null,
			'manufacturer' => 'fastybird',
		]),
		[
			'fb.bus.node.entity.updated.device.hardware' => [
				'id'           => '8059b830-b76d-4f98-be9c-53bd06eab9a5',
				'manufacturer' => 'fastybird',
				'model'        => 'sonoff_basic',
				'version'      => 'rev1',
				'mac_address'  => '80:7d:3a:3d:be:6d',
				'device'       => 'first-device',
				'parent'       => null,
			],
		],
	],
	'messageWithMultiRowUpdate' => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_HARDWARE_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'       => 'first-device',
			'parent'       => null,
			'manufacturer' => 'fastybird',
			'version'      => 'rev2',
		]),
		[
			'fb.bus.node.entity.updated.device.hardware' => [
				'id'           => '8059b830-b76d-4f98-be9c-53bd06eab9a5',
				'manufacturer' => 'fastybird',
				'model'        => 'sonoff_basic',
				'version'      => 'rev2',
				'mac_address'  => '80:7d:3a:3d:be:6d',
				'device'       => 'first-device',
				'parent'       => null,
			],
		],
	],
	'messageWithCreate'         => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_HARDWARE_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'       => 'child-device',
			'parent'       => 'first-device',
			'manufacturer' => 'fastybird',
		]),
		[
			'fb.bus.node.entity.created.device.hardware' => [
				'manufacturer' => 'fastybird',
				'model'        => 'custom',
				'version'      => null,
				'mac_address'  => null,
				'device'       => 'child-device',
				'parent'       => 'first-device',
			],
		],
	],
];
