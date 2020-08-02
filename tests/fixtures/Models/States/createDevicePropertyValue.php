<?php declare(strict_types = 1);

use FastyBird\DevicesNode\Types;

return [
	'one'   => [
		[
			'id'       => '1a30682e-4b38-4aac-8982-6be25c427342',
			'device'   => 'device_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::get(Types\DatatypeType::DATA_TYPE_FLOAT),
			'format'   => [3.0, 30.0],
			'parent'   => null,
		],
		[
			'id'        => '1a30682e-4b38-4aac-8982-6be25c427342',
			'value'     => null,
			'expected'  => null,
			'pending'   => false,
		],
		[
			'id'        => '1a30682e-4b38-4aac-8982-6be25c427342',
			'value'     => null,
			'expected'  => null,
			'pending'   => false,
		],
	],
	'two'   => [
		[
			'id'       => 'c0eadf12-6e70-44af-9106-8ca131e16888',
			'device'   => 'device_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::get(Types\DatatypeType::DATA_TYPE_FLOAT),
			'format'   => [3.0, 30.0],
			'settable' => true,
			'parent'   => null,
		],
		[
			'id'        => 'c0eadf12-6e70-44af-9106-8ca131e16888',
			'value'     => null,
			'expected'  => null,
			'pending'   => false,
		],
		[
			'id'        => 'c0eadf12-6e70-44af-9106-8ca131e16888',
			'value'     => null,
			'expected'  => null,
			'pending'   => false,
		],
	],
	'three' => [
		[
			'id'       => '3123e1ba-4422-4c12-a77b-a87bd3d22c4e',
			'device'   => 'device_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::get(Types\DatatypeType::DATA_TYPE_FLOAT),
			'format'   => [3.0, 30.0],
			'settable' => true,
			'value'    => 3.55,
			'parent'   => null,
		],
		[
			'id'        => '3123e1ba-4422-4c12-a77b-a87bd3d22c4e',
			'value'     => null,
			'expected'  => null,
			'pending'   => false,
		],
		[
			'id'        => '3123e1ba-4422-4c12-a77b-a87bd3d22c4e',
			'value'     => null,
			'expected'  => null,
			'pending'   => false,
		],
	],
];
