<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'update'          => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa',
		file_get_contents(__DIR__ . '/requests/devices.update.json'),
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/devices.update.json',
	],
	'updateNotUnique' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa',
		file_get_contents(__DIR__ . '/requests/devices.update.notUnique.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/devices.update.notUnique.json',
	],
	'invalidType'     => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa',
		file_get_contents(__DIR__ . '/requests/devices.update.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/devices.update.invalidType.json',
	],
	'idMismatch'      => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa',
		file_get_contents(__DIR__ . '/requests/devices.update.idMismatch.json'),
		StatusCodeInterface::STATUS_BAD_REQUEST,
		__DIR__ . '/responses/invalid.identifier.json',
	],
];
