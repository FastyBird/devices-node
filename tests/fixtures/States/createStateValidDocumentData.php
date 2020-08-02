<?php declare(strict_types = 1);

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\States;
use Ramsey\Uuid\Uuid;

return [
	'one' => [
		States\Devices\Property::class,
		[
			'id' => Uuid::uuid4()->toString(),
		],
		Entities\Devices\Properties\Property::class,
	],
	'two' => [
		States\Channels\Property::class,
		[
			'id' => Uuid::uuid4()->toString(),
		],
		Entities\Channels\Properties\Property::class,
	],
];
