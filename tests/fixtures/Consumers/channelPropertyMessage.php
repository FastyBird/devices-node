<?php declare(strict_types = 1);

use FastyBird\DevicesNode;
use Nette\Utils;

return [
	'messageWithUpdate' => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY,
		DevicesNode\Constants::NODE_UI_ORIGIN,
		Utils\Json::encode([
			'device'   => 'first-device',
			'parent'   => null,
			'channel'  => 'channel-one',
			'property' => 'switch',
			'expected' => 'off',
		]),
		[
			'value'    => 'on',
			'expected' => 'off',
			'pending'  => true,
		],
	],
	'messageWithoutUpdate' => [
		DevicesNode\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY,
		DevicesNode\Constants::NODE_UI_ORIGIN,
		Utils\Json::encode([
			'device'   => 'first-device',
			'parent'   => null,
			'channel'  => 'channel-one',
			'property' => 'switch',
			'expected' => 'on',
		]),
		[
			'value'    => 'on',
			'expected' => null,
			'pending'  => false,
		],
	],
];
