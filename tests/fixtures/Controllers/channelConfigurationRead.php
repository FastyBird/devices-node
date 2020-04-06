<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'readAll'                                 => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/configuration',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channel.configuration.index.json',
	],
	'readAllPaging'                           => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/configuration?page[offset]=1&page[limit]=1',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channel.configuration.index.paging.json',
	],
	'readOne'                                 => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/configuration/008d911f-e6d4-4b17-aa28-939839581cde',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channel.configuration.read.json',
	],
	'readOneUnknown'                          => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/configuration/28bc0d38-2f7c-4a71-aa74-27b102f8dfc4',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/channel.configuration.notFound.json',
	],
	'readRelationshipsChannel'                => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/configuration/008d911f-e6d4-4b17-aa28-939839581cde/relationships/channel',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/channel.configuration.readRelationships.channel.json',
	],
	'readRelationshipsChannelUnknownProperty' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/configuration/28bc0d38-2f7c-4a71-aa74-27b102f8dfc4/relationships/channel',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/channel.configuration.notFound.json',
	],
	'readRelationshipsDeviceUnknownProperty'  => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009af/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/configuration/008d911f-e6d4-4b17-aa28-939839581cde/relationships/channel',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/devices.notFound.json',
	],
	'readRelationshipsUnknown'                => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/channels/17c59dfa-2edd-438e-8c49-faa4e38e5a5e/configuration/008d911f-e6d4-4b17-aa28-939839581cde/relationships/unknown',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
];
