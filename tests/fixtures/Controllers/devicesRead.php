<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'readAll'                        => [
		'/v1/devices',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/devices.index.json',
	],
	'readAllPaging'                  => [
		'/v1/devices?page[offset]=1&page[limit]=1',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/devices.index.paging.json',
	],
	'readOne'                        => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/devices.read.json',
	],
	'readOneUnknown'                 => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009af',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/devices.notFound.json',
	],
	'readRelationshipsProperties'    => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/properties',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/devices.readRelationships.properties.json',
	],
	'readRelationshipsConfiguration' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/configuration',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/devices.readRelationships.configuration.json',
	],
	'readRelationshipsChannels'      => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/channels',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/devices.readRelationships.channels.json',
	],
	'readRelationshipsChildren'      => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/children',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/devices.readRelationships.children.json',
	],
	'readRelationshipsHardware'      => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/hardware',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/devices.readRelationships.hardware.json',
	],
	'readRelationshipsFirmware'      => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/firmware',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/devices.readRelationships.firmware.json',
	],
	'readRelationshipsUnknown'       => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/unknown',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
];
