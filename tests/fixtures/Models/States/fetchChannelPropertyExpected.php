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
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_INTEGER,
			'expected' => '10',
		],
		10,
	],
	'two'   => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_BOOLEAN,
			'expected' => '1',
		],
		true,
	],
	'three' => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_BOOLEAN,
			'expected' => null,
		],
		null,
	],
	'four'  => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_INTEGER,
			'expected' => null,
		],
		null,
	],
	'five'  => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_FLOAT,
			'expected' => '10.45',
		],
		10.45,
	],
	'six'   => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_ENUM,
			'expected' => 'test',
		],
		'test',
	],
	'seven' => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_ENUM,
			'format'   => 'one,two,,three',
			'expected' => 'two',
		],
		'two',
	],
	'eight' => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_COLOR,
			'format'   => 'hsv',
			'expected' => '255,255,0',
		],
		'255,255,0',
	],
	'nine'  => [
		$id,
		[
			'id'       => $id->toString(),
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
			'datatype' => Types\DatatypeType::DATA_TYPE_COLOR,
			'format'   => 'hsv',
		],
		null,
	],
];
