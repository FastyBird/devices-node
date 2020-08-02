<?php declare(strict_types = 1);

use FastyBird\DevicesNode\Types;

return [
	'one'   => [
		[
			'id'       => '6645f364-efc5-407c-ad6c-eb888afb54fe',
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::get(Types\DatatypeType::DATA_TYPE_FLOAT),
			'format'   => [3.0, 30.0],
			'parent'   => null,
		],
		[
			'id'       => '6645f364-efc5-407c-ad6c-eb888afb54fe',
			'value'    => null,
			'expected' => null,
			'pending'  => false,
		],
		[
			'id'       => '6645f364-efc5-407c-ad6c-eb888afb54fe',
			'value'    => null,
			'expected' => null,
			'pending'  => false,
		],
	],
	'two'   => [
		[
			'id'       => '97d926ad-2c79-4cfd-abc4-d469c6ff68eb',
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::get(Types\DatatypeType::DATA_TYPE_FLOAT),
			'format'   => [3.0, 30.0],
			'settable' => true,
			'parent'   => null,
		],
		[
			'id'       => '97d926ad-2c79-4cfd-abc4-d469c6ff68eb',
			'value'    => null,
			'expected' => null,
			'pending'  => false,
		],
		[
			'id'       => '97d926ad-2c79-4cfd-abc4-d469c6ff68eb',
			'value'    => null,
			'expected' => null,
			'pending'  => false,
		],
	],
	'three' => [
		[
			'id'       => '1c670149-d999-49a0-a74f-cef22b35666a',
			'value'    => 3.55,
			'datatype' => Types\DatatypeType::get(Types\DatatypeType::DATA_TYPE_FLOAT),
		],
		[
			'id'       => '1c670149-d999-49a0-a74f-cef22b35666a',
			'value'    => null,
			'expected' => null,
			'pending'  => false,
		],
		[
			'id'       => '1c670149-d999-49a0-a74f-cef22b35666a',
			'value'    => null,
			'expected' => null,
			'pending'  => false,
		],
	],
];
