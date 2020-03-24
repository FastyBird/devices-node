<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'read' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/firmware',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.firmware.read.json',
	],
	'readRelationshipsDevice' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/firmware/relationships/device',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.firmware.readRelationships.device.json',
	],
	'readRelationshipsUnknown' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/firmware/relationships/unknown',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
];
