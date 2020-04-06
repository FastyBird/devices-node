<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'readAll'                        => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channels.index.json',
	],
	'readAllPaging'                  => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels?page[offset]=1&page[limit]=1',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channels.index.paging.json',
	],
	'readAllUnknownDevice'           => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009af/channels',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/devices.notFound.json',
	],
	'readOne'                        => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channels.read.json',
	],
	'readOneUnknown'                 => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5ae5',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/channels.notFound.json',
	],
	'readOneUnknownDevice'           => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009af/channels/17c59dfa-2edd-438e-8c49-faa4e38e5ae5',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/devices.notFound.json',
	],
	'readRelationshipsProperties'    => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/relationships/properties',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channels.readRelationships.properties.json',
	],
	'readRelationshipsConfiguration' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/relationships/configuration',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channels.readRelationships.configuration.json',
	],
	'readRelationshipsUnknown'       => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/relationships/unknown',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
];
