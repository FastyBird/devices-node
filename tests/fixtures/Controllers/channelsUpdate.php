<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'update'      => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e',
		file_get_contents(__DIR__ . '/requests/channels.update.json'),
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channels.update.json',
	],
	'invalidType' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e',
		file_get_contents(__DIR__ . '/requests/channels.update.invalidType.json'),
		StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
		__DIR__ . '/responses/channels.update.invalidType.json',
	],
	'idMismatch'  => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e',
		file_get_contents(__DIR__ . '/requests/channels.update.idMismatch.json'),
		StatusCodeInterface::STATUS_BAD_REQUEST,
		__DIR__ . '/responses/invalid.identifier.json',
	],
];
