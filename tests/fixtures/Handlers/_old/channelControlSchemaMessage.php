<?php declare(strict_types = 1);

use FastyBird\DevicesModule;
use FastyBird\ModulesMetadata;
use Nette\Utils;

return [
	'messageWithoutUpdate' => [
		ModulesMetadata\Constants::MESSAGE_BUS_CHANNELS_CONTROLS_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'parent'  => null,
			'channel' => 'channel-one',
			'control' => 'configure',
			'schema'  => [
				[
					'configuration' => 'pulse_mode',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_SELECT,
					'default'       => 0,
					'values'        => [
						[
							'value' => 0,
							'name'  => 'disabled',
						],
						[
							'value' => 1,
							'name'  => 'normally_off',
						],
						[
							'value' => 2,
							'name'  => 'normally_on',
						],
					],
				],
				[
					'configuration' => 'relay_boot',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_SELECT,
					'default'       => 0,
					'values'        => [
						[
							'value' => 0,
							'name'  => 'always_off',
						],
						[
							'value' => 1,
							'name'  => 'always_on',
						],
						[
							'value' => 2,
							'name'  => 'same_before',
						],
						[
							'value' => 3,
							'name'  => 'toggle_before',
						],
					],
				],
				[
					'configuration' => 'pulse_time',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_NUMBER,
					'default'       => 1,
					'min'           => 1,
					'max'           => 60,
					'step'          => 0.1,
				],
				[
					'configuration' => 'on_disconnect',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_SELECT,
					'default'       => 0,
					'values'        => [
						[
							'value' => 0,
							'name'  => 'no_change',
						],
						[
							'value' => 1,
							'name'  => 'turn_off',
						],
						[
							'value' => 2,
							'name'  => 'turn_on',
						],
					],
				],
			],
		]),
		[],
	],
	'messageWithUpdate'    => [
		ModulesMetadata\Constants::MESSAGE_BUS_CHANNELS_CONTROLS_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'parent'  => null,
			'channel' => 'channel-one',
			'control' => 'configure',
			'schema'  => [
				[
					'configuration' => 'pulse_mode',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_SELECT,
					'default'       => 0,
					'values'        => [
						[
							'value' => 0,
							'name'  => 'disabled',
						],
						[
							'value' => 1,
							'name'  => 'normally_off',
						],
						[
							'value' => 2,
							'name'  => 'normally_on',
						],
					],
				],
				[
					'configuration' => 'relay_boot',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_SELECT,
					'default'       => 0,
					'values'        => [
						[
							'value' => 0,
							'name'  => 'always_off',
						],
						[
							'value' => 1,
							'name'  => 'always_on',
						],
						[
							'value' => 2,
							'name'  => 'same_before',
						],
						[
							'value' => 3,
							'name'  => 'toggle_before',
						],
					],
				],
				[
					'configuration' => 'pulse_time',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_NUMBER,
					'default'       => 1,
					'min'           => 1,
					'max'           => 60,
					'step'          => 0.1,
				],
				[
					'configuration' => 'on_disconnect',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_SELECT,
					'default'       => 2,
					'values'        => [
						[
							'value' => 0,
							'name'  => 'no_change',
						],
						[
							'value' => 1,
							'name'  => 'turn_off',
						],
						[
							'value' => 2,
							'name'  => 'other_action',
						],
					],
				],
			],
		]),
		[
			'fb.bus.entity.updated.channel.configuration' => [
				'id'            => 'c747cfdd-654c-4e50-9715-6d14dbf20552',
				'configuration' => 'on_disconnect',
				'name'          => null,
				'comment'       => null,
				'default'       => '2',
				'value'         => null,
				'values'        => [
					['name' => 'no_change', 'value' => '0'],
					['name' => 'turn_off', 'value' => '1'],
					['name' => 'other_action', 'value' => '2'],
				],
				'type'          => DevicesModule\Constants::DATA_TYPE_SELECT,
				'device'        => 'first-device',
				'owner'         => '455354e8-96bd-4c29-84e7-9f10e1d4db4b',
				'parent'        => null,
				'channel'       => 'channel-one',
			],
		],
	],
	'messageWithDelete'    => [
		ModulesMetadata\Constants::MESSAGE_BUS_CHANNELS_CONTROLS_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'parent'  => null,
			'channel' => 'channel-one',
			'control' => 'configure',
			'schema'  => [
				[
					'configuration' => 'pulse_mode',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_SELECT,
					'default'       => 0,
					'values'        => [
						[
							'value' => 0,
							'name'  => 'disabled',
						],
						[
							'value' => 1,
							'name'  => 'normally_off',
						],
						[
							'value' => 2,
							'name'  => 'normally_on',
						],
					],
				],
				[
					'configuration' => 'relay_boot',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_SELECT,
					'default'       => 0,
					'values'        => [
						[
							'value' => 0,
							'name'  => 'always_off',
						],
						[
							'value' => 1,
							'name'  => 'always_on',
						],
						[
							'value' => 2,
							'name'  => 'same_before',
						],
						[
							'value' => 3,
							'name'  => 'toggle_before',
						],
					],
				],
				[
					'configuration' => 'pulse_time',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_NUMBER,
					'default'       => 1,
					'min'           => 1,
					'max'           => 60,
					'step'          => 0.1,
				],
			],
		]),
		[
			'fb.bus.entity.deleted.channel.configuration' => [
				'id'            => 'c747cfdd-654c-4e50-9715-6d14dbf20552',
				'configuration' => 'on_disconnect',
				'name'          => null,
				'comment'       => null,
				'default'       => '0',
				'value'         => null,
				'values'        => [
					['name' => 'no_change', 'value' => '0'],
					['name' => 'turn_off', 'value' => '1'],
					['name' => 'turn_on', 'value' => '2'],
				],
				'type'          => 'select',
				'device'        => 'first-device',
				'owner'         => '455354e8-96bd-4c29-84e7-9f10e1d4db4b',
				'parent'        => null,
				'channel'       => 'channel-one',
			],
		],
	],
	'messageWithCreate'    => [
		ModulesMetadata\Constants::MESSAGE_BUS_CHANNELS_CONTROLS_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'parent'  => null,
			'channel' => 'channel-one',
			'control' => 'configure',
			'schema'  => [
				[
					'configuration' => 'pulse_mode',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_SELECT,
					'default'       => 0,
					'values'        => [
						[
							'value' => 0,
							'name'  => 'disabled',
						],
						[
							'value' => 1,
							'name'  => 'normally_off',
						],
						[
							'value' => 2,
							'name'  => 'normally_on',
						],
					],
				],
				[
					'configuration' => 'relay_boot',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_SELECT,
					'default'       => 0,
					'values'        => [
						[
							'value' => 0,
							'name'  => 'always_off',
						],
						[
							'value' => 1,
							'name'  => 'always_on',
						],
						[
							'value' => 2,
							'name'  => 'same_before',
						],
						[
							'value' => 3,
							'name'  => 'toggle_before',
						],
					],
				],
				[
					'configuration' => 'pulse_time',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_NUMBER,
					'default'       => 1,
					'min'           => 1,
					'max'           => 60,
					'step'          => 0.1,
				],
				[
					'configuration' => 'on_disconnect',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_SELECT,
					'default'       => 0,
					'values'        => [
						[
							'value' => 0,
							'name'  => 'no_change',
						],
						[
							'value' => 1,
							'name'  => 'turn_off',
						],
						[
							'value' => 2,
							'name'  => 'turn_on',
						],
					],
				],
				[
					'configuration' => 'new_attribute',
					'name'          => null,
					'comment'       => null,
					'type'          => DevicesModule\Constants::DATA_TYPE_NUMBER,
					'default'       => null,
					'min'           => 50,
					'max'           => 60,
					'step'          => 0.1,
				],
			],
		]),
		[
			'fb.bus.entity.created.channel.configuration' => [
				'configuration' => 'new_attribute',
				'name'          => null,
				'comment'       => null,
				'default'       => null,
				'value'         => null,
				'min'           => 50.0,
				'max'           => 60.0,
				'step'          => 0.1,
				'type'          => DevicesModule\Constants::DATA_TYPE_NUMBER,
				'device'        => 'first-device',
				'owner'         => '455354e8-96bd-4c29-84e7-9f10e1d4db4b',
				'parent'        => null,
				'channel'       => 'channel-one',
			],
		],
	],
];
