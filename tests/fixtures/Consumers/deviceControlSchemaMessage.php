<?php declare(strict_types = 1);

use FastyBird\DevicesNode;
use Nette\Utils;

return [
	'messageWithoutUpdate' => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CONTROLS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'control' => 'configure',
			'schema'  => [
				[
					'name'    => 'sensor_expected_power',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => null,
					'min'     => 0,
					'max'     => 500,
					'step'    => 1,
				],
				[
					'name'    => 'btn_delay',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 500,
					'min'     => 0,
					'max'     => 1000,
					'step'    => 100,
				],
				[
					'name'    => 'sensor_energy_units',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
						[
							'value' => 0,
							'name'  => 'joules',
						],
						[
							'value' => 1,
							'name'  => 'kilowatts_hours',
						],
					],
				],
				[
					'name'    => 'sensor_expected_current',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => null,
					'min'     => 0,
					'max'     => 500,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_expected_voltage',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => null,
					'min'     => 0,
					'max'     => 500,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_save_interval',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 0,
					'min'     => 0,
					'max'     => 200,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_read_interval',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 6,
					'values'  => [
						[
							'value' => 1,
							'name'  => '1',
						],
						[
							'value' => 6,
							'name'  => '6',
						],
						[
							'value' => 10,
							'name'  => '10',
						],
						[
							'value' => 15,
							'name'  => '15',
						],
						[
							'value' => 30,
							'name'  => '30',
						],
						[
							'value' => 60,
							'name'  => '60',
						],
						[
							'value' => 300,
							'name'  => '300',
						],
						[
							'value' => 600,
							'name'  => '600',
						],
						[
							'value' => 900,
							'name'  => '900',
						],
						[
							'value' => 1800,
							'name'  => '1800',
						],
						[
							'value' => 3600,
							'name'  => '3600',
						],
					],
				],
				[
					'name'    => 'sensor_report_interval',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 10,
					'min'     => 1,
					'max'     => 60,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_power_units',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
						[
							'value' => 0,
							'name'  => 'watts',
						],
						[
							'value' => 1,
							'name'  => 'kilowatts',
						],
					],
				],
			],
		]),
		[],
	],
	'messageWithUpdate'    => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CONTROLS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'control' => 'configure',
			'schema'  => [
				[
					'name'    => 'sensor_expected_power',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => null,
					'min'     => 100,
					'max'     => 200,
					'step'    => 1,
				],
				[
					'name'    => 'btn_delay',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 500,
					'min'     => 0,
					'max'     => 1000,
					'step'    => 100,
				],
				[
					'name'    => 'sensor_energy_units',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
						[
							'value' => 0,
							'name'  => 'joules',
						],
						[
							'value' => 1,
							'name'  => 'kilowatts_hours',
						],
					],
				],
				[
					'name'    => 'sensor_expected_current',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => null,
					'min'     => 0,
					'max'     => 500,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_expected_voltage',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => null,
					'min'     => 0,
					'max'     => 500,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_save_interval',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 0,
					'min'     => 0,
					'max'     => 200,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_read_interval',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 6,
					'values'  => [
						[
							'value' => 1,
							'name'  => '1',
						],
						[
							'value' => 6,
							'name'  => '6',
						],
						[
							'value' => 10,
							'name'  => '10',
						],
						[
							'value' => 15,
							'name'  => '15',
						],
						[
							'value' => 30,
							'name'  => '30',
						],
						[
							'value' => 60,
							'name'  => '60',
						],
						[
							'value' => 300,
							'name'  => '300',
						],
						[
							'value' => 600,
							'name'  => '600',
						],
						[
							'value' => 900,
							'name'  => '900',
						],
						[
							'value' => 1800,
							'name'  => '1800',
						],
						[
							'value' => 3600,
							'name'  => '3600',
						],
					],
				],
				[
					'name'    => 'sensor_report_interval',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 10,
					'min'     => 1,
					'max'     => 60,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_power_units',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
						[
							'value' => 0,
							'name'  => 'watts',
						],
						[
							'value' => 1,
							'name'  => 'kilowatts',
						],
					],
				],
			],
		]),
		[
			'fb.bus.node.entity.updated.device.configuration' => [
				'id'      => '138c6cfc-ed49-476b-9f1e-6ee1dcb24f0b',
				'name'    => 'sensor_expected_power',
				'title'   => null,
				'comment' => null,
				'default' => null,
				'value'   => null,
				'min'     => 100.0,
				'max'     => 200.0,
				'step'    => 1.0,
				'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
				'device'  => 'first-device',
			],
		],
	],
	'messageWithDelete'    => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CONTROLS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'control' => 'configure',
			'schema'  => [
				[
					'name'    => 'sensor_expected_power',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => null,
					'min'     => 0,
					'max'     => 500,
					'step'    => 1,
				],
				[
					'name'    => 'btn_delay',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 500,
					'min'     => 0,
					'max'     => 1000,
					'step'    => 100,
				],
				[
					'name'    => 'sensor_energy_units',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
						[
							'value' => 0,
							'name'  => 'joules',
						],
						[
							'value' => 1,
							'name'  => 'kilowatts_hours',
						],
					],
				],
				[
					'name'    => 'sensor_expected_current',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => null,
					'min'     => 0,
					'max'     => 500,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_expected_voltage',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => null,
					'min'     => 0,
					'max'     => 500,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_save_interval',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 0,
					'min'     => 0,
					'max'     => 200,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_read_interval',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 6,
					'values'  => [
						[
							'value' => 1,
							'name'  => '1',
						],
						[
							'value' => 6,
							'name'  => '6',
						],
						[
							'value' => 10,
							'name'  => '10',
						],
						[
							'value' => 15,
							'name'  => '15',
						],
						[
							'value' => 30,
							'name'  => '30',
						],
						[
							'value' => 60,
							'name'  => '60',
						],
						[
							'value' => 300,
							'name'  => '300',
						],
						[
							'value' => 600,
							'name'  => '600',
						],
						[
							'value' => 900,
							'name'  => '900',
						],
						[
							'value' => 1800,
							'name'  => '1800',
						],
						[
							'value' => 3600,
							'name'  => '3600',
						],
					],
				],
				[
					'name'    => 'sensor_report_interval',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 10,
					'min'     => 1,
					'max'     => 60,
					'step'    => 1,
				],
			],
		]),
		[
			'fb.bus.node.entity.deleted.device.configuration' => [
				'id'      => '8d933e4c-1fc9-4361-ba09-eebee4592776',
				'name'    => 'sensor_power_units',
				'title'   => null,
				'comment' => null,
				'default' => '0',
				'value'   => null,
				'values'  => [
					['name' => 'watts', 'value' => '0'],
					['name' => 'kilowatts', 'value' => '1'],
				],
				'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
				'device'  => 'first-device',
			],
		],
	],
	'messageWithCreate'    => [
		DevicesNode\Constants::RABBIT_MQ_DEVICES_CONTROLS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'control' => 'configure',
			'schema'  => [
				[
					'name'    => 'sensor_expected_power',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => null,
					'min'     => 0,
					'max'     => 500,
					'step'    => 1,
				],
				[
					'name'    => 'btn_delay',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 500,
					'min'     => 0,
					'max'     => 1000,
					'step'    => 100,
				],
				[
					'name'    => 'sensor_energy_units',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
						[
							'value' => 0,
							'name'  => 'joules',
						],
						[
							'value' => 1,
							'name'  => 'kilowatts_hours',
						],
					],
				],
				[
					'name'    => 'sensor_expected_current',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => null,
					'min'     => 0,
					'max'     => 500,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_expected_voltage',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => null,
					'min'     => 0,
					'max'     => 500,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_save_interval',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 0,
					'min'     => 0,
					'max'     => 200,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_read_interval',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 6,
					'values'  => [
						[
							'value' => 1,
							'name'  => '1',
						],
						[
							'value' => 6,
							'name'  => '6',
						],
						[
							'value' => 10,
							'name'  => '10',
						],
						[
							'value' => 15,
							'name'  => '15',
						],
						[
							'value' => 30,
							'name'  => '30',
						],
						[
							'value' => 60,
							'name'  => '60',
						],
						[
							'value' => 300,
							'name'  => '300',
						],
						[
							'value' => 600,
							'name'  => '600',
						],
						[
							'value' => 900,
							'name'  => '900',
						],
						[
							'value' => 1800,
							'name'  => '1800',
						],
						[
							'value' => 3600,
							'name'  => '3600',
						],
					],
				],
				[
					'name'    => 'sensor_report_interval',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 10,
					'min'     => 1,
					'max'     => 60,
					'step'    => 1,
				],
				[
					'name'    => 'sensor_power_units',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
						[
							'value' => 0,
							'name'  => 'watts',
						],
						[
							'value' => 1,
							'name'  => 'kilowatts',
						],
					],
				],
				[
					'name'    => 'testing_text',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_TEXT,
					'default' => 'someval',
					'values'  => [
						[
							'value' => 0,
							'name'  => 'watts',
						],
						[
							'value' => 1,
							'name'  => 'kilowatts',
						],
					],
				],
			],
		]),
		[
			'fb.bus.node.entity.created.device.configuration' => [
				'name'    => 'testing_text',
				'title'   => null,
				'comment' => null,
				'default' => 'someval',
				'value'   => null,
				'type'    => DevicesNode\Constants::DATA_TYPE_TEXT,
				'device'  => 'first-device',
			],
		],
	],
];
