<?php declare(strict_types = 1);

return [
	'readAll' => [
		'/v1/devices',
		__DIR__ . '/responses/devices.index.json',
	],
	'readAllPaging' => [
		'/v1/devices?page[offset]=1&page[limit]=1',
		__DIR__ . '/responses/devices.index.paging.json',
	],
	'readOne' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa',
		__DIR__ . '/responses/devices.read.json',
	],
	'readOneUnknown' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009af',
		__DIR__ . '/responses/devices.read.notFound.json',
	],
	'readRelationshipsProperties' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/properties',
		__DIR__ . '/responses/devices.readRelationships.properties.json',
	],
	'readRelationshipsConfiguration' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/configuration',
		__DIR__ . '/responses/devices.readRelationships.configuration.json',
	],
	'readRelationshipsChannels' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/channels',
		__DIR__ . '/responses/devices.readRelationships.channels.json',
	],
	'readRelationshipsChildren' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/children',
		__DIR__ . '/responses/devices.readRelationships.children.json',
	],
	'readRelationshipsCredentials' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/credentials',
		__DIR__ . '/responses/devices.readRelationships.credentials.json',
	],
	'readRelationshipsHardware' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/hardware',
		__DIR__ . '/responses/devices.readRelationships.hardware.json',
	],
	'readRelationshipsFirmware' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/firmware',
		__DIR__ . '/responses/devices.readRelationships.firmware.json',
	],
	'readRelationshipsUnknown' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/relationships/unknown',
		__DIR__ . '/responses/relation.unknown.json',
	],
];
