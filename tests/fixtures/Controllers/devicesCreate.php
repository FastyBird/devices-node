<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'create' => [
		'/v1/devices',
		file_get_contents(__DIR__ . '/requests/devices.create.json'),
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/devices.create.json',
	],
	'missingRequired' => [
		'/v1/devices',
		file_get_contents(__DIR__ . '/requests/devices.create.missing.required.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/missing.required.json',
	],
	'missingRelation' => [
		'/v1/devices',
		file_get_contents(__DIR__ . '/requests/devices.create.missing.relation.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/devices.create.missing.relation.json',
	],
	'invalidType' => [
		'/v1/devices',
		file_get_contents(__DIR__ . '/requests/devices.create.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/devices.create.invalidType.json',
	],
];
