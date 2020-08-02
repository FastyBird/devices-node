<?php declare(strict_types = 1);

use FastyBird\DevicesNode\Types;

return [
	'one'   => [
		[
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_INTEGER,
			'format'   => null,
		],
	],
	'two'   => [
		[
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_INTEGER,
			'format'   => '0:10',
		],
	],
	'three' => [
		[
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'format'   => '0:10',
			'settable' => true,
		],
	],
	'four'  => [
		[
			'device'    => 'device_name',
			'channel'   => 'channel_name',
			'property'  => 'property_name',
			'format'    => '0:10',
			'settable'  => true,
			'queryable' => true,
		],
	],
];
