<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'readAll' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/properties',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channel.properties.index.json',
	],
	'readAllPaging' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/properties?page[offset]=1&page[limit]=1',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channel.properties.index.paging.json',
	],
	'readOne' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/properties/bbcccf8c-33ab-431b-a795-d7bb38b6b6db',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channel.properties.read.json',
	],
	'readOneUnknown' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/properties/28bc0d38-2f7c-4a71-aa74-27b102f8dfc4',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/channel.properties.notFound.json',
	],
	'readRelationshipsChannel' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/properties/bbcccf8c-33ab-431b-a795-d7bb38b6b6db/relationships/channel',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channel.properties.readRelationships.channel.json',
	],
	'readRelationshipsChannelUnknownProperty' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/properties/28bc0d38-2f7c-4a71-aa74-27b102f8dfc4/relationships/channel',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/channel.properties.notFound.json',
	],
	'readRelationshipsDeviceUnknownProperty' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009af/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/properties/bbcccf8c-33ab-431b-a795-d7bb38b6b6db/relationships/channel',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/devices.notFound.json',
	],
	'readRelationshipsUnknown' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/properties/bbcccf8c-33ab-431b-a795-d7bb38b6b6db/relationships/unknown',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
];
