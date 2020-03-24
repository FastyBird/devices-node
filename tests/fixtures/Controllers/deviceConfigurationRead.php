<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'readAll' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.configuration.index.json',
	],
	'readAllPaging' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration?page[offset]=1&page[limit]=1',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.configuration.index.paging.json',
	],
	'readOne' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/138c6cfc-ed49-476b-9f1e-6ee1dcb24f0b',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.configuration.read.json',
	],
	'readOneUnknown' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/28bc0d38-2f7c-4a71-aa74-27b102f8dfc4',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/device.configuration.notFound.json',
	],
	'readRelationshipsDevice' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/138c6cfc-ed49-476b-9f1e-6ee1dcb24f0b/relationships/device',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.configuration.readRelationships.device.json',
	],
	'readRelationshipsDeviceUnknownProperty' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/28bc0d38-2f7c-4a71-aa74-27b102f8dfc4/relationships/device',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/device.configuration.notFound.json',
	],
	'readRelationshipsUnknown' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/138c6cfc-ed49-476b-9f1e-6ee1dcb24f0b/relationships/unknown',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
];
