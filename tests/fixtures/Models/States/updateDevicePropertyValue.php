<?php declare(strict_types = 1);

use FastyBird\DevicesNode\Types;
use Ramsey\Uuid;

$id = Uuid\Uuid::uuid4()->toString();

return [
	'one'   => [
		[
			'datatype' => Types\DatatypeType::get(Types\DatatypeType::DATA_TYPE_STRING),
			'settable' => true,
		],
		[
			'id'       => $id,
			'value'    => null,
			'expected' => null,
			'pending'  => false,
		],
		[
			'id'       => $id,
			'value'    => null,
			'expected' => null,
			'pending'  => false,
		],
	],
	'two'   => [
		[
			'datatype'  => Types\DatatypeType::get(Types\DatatypeType::DATA_TYPE_INTEGER),
			'settable'  => true,
			'queryable' => true,
		],
		[
			'id'       => $id,
			'value'    => null,
			'expected' => null,
			'pending'  => false,
		],
		[
			'id'       => $id,
			'value'    => null,
			'expected' => null,
			'pending'  => false,
		],
	],
	'three' => [
		[
			'datatype'  => Types\DatatypeType::get(Types\DatatypeType::DATA_TYPE_ENUM),
			'queryable' => true,
		],
		[
			'id'       => $id,
			'value'    => null,
			'expected' => null,
			'pending'  => false,
		],
		[
			'id'       => $id,
			'value'    => null,
			'expected' => null,
			'pending'  => false,
		],
	],
	'four'  => [
		[
			'datatype'  => Types\DatatypeType::get(Types\DatatypeType::DATA_TYPE_FLOAT),
			'queryable' => true,
			'value'     => 10.33,
		],
		[
			'id'       => $id,
			'value'    => null,
			'expected' => null,
			'pending'  => false,
		],
		[
			'id'       => $id,
			'value'    => 10.33,
			'expected' => null,
			'pending'  => false,
		],
	],
];
