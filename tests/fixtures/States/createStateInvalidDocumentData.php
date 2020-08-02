<?php declare(strict_types = 1);

use FastyBird\DevicesNode\States;
use Ramsey\Uuid\Uuid;

return [
	'one'   => [
		States\Devices\Property::class,
		[
			'device'   => 'device-name',
			'property' => 'property-name',
		],
	],
	'two'   => [
		States\Devices\Property::class,
		[
			'id'       => 'invalid-string',
			'device'   => 'device-name',
			'property' => 'property-name',
		],
	],
	'three' => [
		States\Channels\Property::class,
		[
			'id'       => Uuid::uuid4()->toString(),
			'device'   => 'device-name',
			'channel'  => 'channel-name',
			'property' => 'property-name',
			'datatype' => 'not-valid',
			'format'   => null,
			'settable' => true,
		],
	],
];
