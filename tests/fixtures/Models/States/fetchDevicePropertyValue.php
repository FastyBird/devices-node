<?php declare(strict_types = 1);

use FastyBird\DevicesNode\Types;
use Ramsey\Uuid\Uuid;

$id = Uuid::uuid4();

return [
	'one'   => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_INTEGER,
			'value'    => '10',
		],
		10,
	],
	'two'   => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_BOOLEAN,
			'value'    => '1',
		],
		true,
	],
	'three' => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_BOOLEAN,
			'value'    => null,
		],
		null,
	],
	'four'  => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_INTEGER,
			'value'    => null,
		],
		null,
	],
	'five'  => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_FLOAT,
			'value'    => '10.45',
		],
		10.45,
	],
	'six'   => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_ENUM,
			'value'    => 'test',
		],
		'test',
	],
	'seven' => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_ENUM,
			'format'   => 'one,two,,three',
			'value'    => 'two',
		],
		'two',
	],
	'eight' => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_COLOR,
			'format'   => 'hsv',
			'value'    => '255,255,0',
		],
		'255,255,0',
	],
	'nine'  => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_COLOR,
			'format'   => 'hsv',
		],
		null,
	],
];
