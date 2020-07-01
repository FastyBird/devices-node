<?php declare(strict_types = 1);

use FastyBird\DevicesNode;
use Nette\Utils;

return [
	'messageWithoutUpdate' => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_CONTROLS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'parent'  => null,
			'channel' => 'channel-one',
			'control' => 'configure',
			'schema'  => [
				[
					'name'    => 'pulse_mode',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
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
					'name'    => 'relay_boot',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
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
					'name'    => 'pulse_time',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 1,
					'min'     => 1,
					'max'     => 60,
					'step'    => 0.1,
				],
				[
					'name'    => 'on_disconnect',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
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
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_CONTROLS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'parent'  => null,
			'channel' => 'channel-one',
			'control' => 'configure',
			'schema'  => [
				[
					'name'    => 'pulse_mode',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
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
					'name'    => 'relay_boot',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
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
					'name'    => 'pulse_time',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 1,
					'min'     => 1,
					'max'     => 60,
					'step'    => 0.1,
				],
				[
					'name'    => 'on_disconnect',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 2,
					'values'  => [
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
			'fb.bus.node.entity.updated.channel.configuration' => [
				'id'      => 'c747cfdd-654c-4e50-9715-6d14dbf20552',
				'name'    => 'on_disconnect',
				'title'   => null,
				'comment' => null,
				'default' => '2',
				'value'   => null,
				'values'  => [
					['name' => 'no_change', 'value' => '0'],
					['name' => 'turn_off', 'value' => '1'],
					['name' => 'other_action', 'value' => '2'],
				],
				'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
				'device'  => 'first-device',
				'parent'  => null,
				'channel' => 'channel-one',
			],
		],
	],
	'messageWithDelete'    => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_CONTROLS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'parent'  => null,
			'channel' => 'channel-one',
			'control' => 'configure',
			'schema'  => [
				[
					'name'    => 'pulse_mode',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
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
					'name'    => 'relay_boot',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
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
					'name'    => 'pulse_time',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 1,
					'min'     => 1,
					'max'     => 60,
					'step'    => 0.1,
				],
			],
		]),
		[
			'fb.bus.node.entity.deleted.channel.configuration' => [
				'id'      => 'c747cfdd-654c-4e50-9715-6d14dbf20552',
				'name'    => 'on_disconnect',
				'title'   => null,
				'comment' => null,
				'default' => '0',
				'value'   => null,
				'values'  => [
					['name' => 'no_change', 'value' => '0'],
					['name' => 'turn_off', 'value' => '1'],
					['name' => 'turn_on', 'value' => '2'],
				],
				'type'    => 'select',
				'device'  => 'first-device',
				'parent'  => null,
				'channel' => 'channel-one',
			],
		],
	],
	'messageWithCreate'    => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_CONTROLS_DATA_ROUTING_KEY,
		Utils\ArrayHash::from([
			'device'  => 'first-device',
			'parent'  => null,
			'channel' => 'channel-one',
			'control' => 'configure',
			'schema'  => [
				[
					'name'    => 'pulse_mode',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
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
					'name'    => 'relay_boot',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
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
					'name'    => 'pulse_time',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => 1,
					'min'     => 1,
					'max'     => 60,
					'step'    => 0.1,
				],
				[
					'name'    => 'on_disconnect',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_SELECT,
					'default' => 0,
					'values'  => [
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
					'name'    => 'new_attribute',
					'title'   => null,
					'comment' => null,
					'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
					'default' => null,
					'min'     => 50,
					'max'     => 60,
					'step'    => 0.1,
				],
			],
		]),
		[
			'fb.bus.node.entity.created.channel.configuration' => [
				'name'    => 'new_attribute',
				'title'   => null,
				'comment' => null,
				'default' => null,
				'value'   => null,
				'min'     => 50.0,
				'max'     => 60.0,
				'step'    => 0.1,
				'type'    => DevicesNode\Constants::DATA_TYPE_NUMBER,
				'device'  => 'first-device',
				'parent'  => null,
				'channel' => 'channel-one',
			],
		],
	],
];
