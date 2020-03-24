<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'read' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/credentials',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.credentials.read.json',
	],
	'readRelationshipsDevice' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/credentials/relationships/device',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.credentials.readRelationships.device.json',
	],
	'readRelationshipsUnknown' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/credentials/relationships/unknown',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
];
