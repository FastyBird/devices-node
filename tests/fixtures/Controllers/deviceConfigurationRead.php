<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

const VALID_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const EXPIRED_TOKEN = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3MjFlMTAyNS04Zjc4LTQzOGQtODIwZi0wZDQ2OWEzNzk1NWQiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU3Nzg4MDAwMCwiZXhwIjoxNTc3OTAxNjAwLCJ1c2VyIjoiNTI1ZDZhMDktN2MwNi00NmQyLWFmZmEtNzA5YmIxODM3MDdlIiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.F9veOiNfcqQVxpbMF7OY5j1AcPLpPQb8dEIZbrBmh24';
const INVALID_TOKEN = 'eyJqdGkiOiI5YWY1NjI0Mi01ZDg3LTQzNjQtYmIxZS1kOWZjODI4NmIzZmYiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiNWU3OWVmYmYtYmQwZC01YjdjLTQ2ZWYtYmZiZGVmYmZiZDM0Iiwicm9sZXMiOlsiYWRtaW5pc3RyYXRvciJdfQ.Lb-zUa9DL7swdVSEuPTqaR9FvLgKwuEtrhxiJFWjhU8';
const VALID_TOKEN_USER = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJqdGkiOiI3YzVkNzdhZC1kOTNlLTRjMmMtOThlNS05ZTFhZmM0NDQ2MTUiLCJpc3MiOiJjb20uZmFzdHliaXJkLmF1dGgtbm9kZSIsImlhdCI6MTU4NTc0MjQwMCwiZXhwIjoxNTg1NzQ5NjAwLCJ1c2VyIjoiZWZiZmJkZWYtYmZiZC02OGVmLWJmYmQtNzcwYjQwZWZiZmJkIiwicm9sZXMiOlsidXNlciJdfQ.cbatWCuGX-K8XbF9MMN7DqxV9hriWmUSGcDGGmnxXX0';

return [
	// Valid responses
	//////////////////
	'readAll'                                     => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration',
		'Bearer ' . VALID_TOKEN,
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.configuration.index.json',
	],
	'readAllPaging'                               => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration?page[offset]=1&page[limit]=1',
		'Bearer ' . VALID_TOKEN,
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.configuration.index.paging.json',
	],
	'readOne'                                     => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/138c6cfc-ed49-476b-9f1e-6ee1dcb24f0b',
		'Bearer ' . VALID_TOKEN,
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.configuration.read.json',
	],
	'readRelationshipsDevice'                     => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/138c6cfc-ed49-476b-9f1e-6ee1dcb24f0b/relationships/device',
		'Bearer ' . VALID_TOKEN,
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.configuration.readRelationships.device.json',
	],

	// Invalid responses
	////////////////////
	'readOneUnknown'                              => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/28bc0d38-2f7c-4a71-aa74-27b102f8dfc4',
		'Bearer ' . VALID_TOKEN,
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/device.configuration.notFound.json',
	],
	'readRelationshipsDeviceUnknownConfiguration' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/28bc0d38-2f7c-4a71-aa74-27b102f8dfc4/relationships/device',
		'Bearer ' . VALID_TOKEN,
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/device.configuration.notFound.json',
	],
	'readRelationshipsUnknown'                    => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/138c6cfc-ed49-476b-9f1e-6ee1dcb24f0b/relationships/unknown',
		'Bearer ' . VALID_TOKEN,
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/relation.unknown.json',
	],
	'readRelationshipsUnknownDevice' => [
		'/v1/devices/28bc0d38-2f7c-4a71-aa74-27b102f8dfc4/configuration/138c6cfc-ed49-476b-9f1e-6ee1dcb24f0b/relationships/device',
		'Bearer ' . VALID_TOKEN,
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/devices.notFound.json',
	],
	'readAllMissingToken'            => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration',
		null,
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/forbidden.json',
	],
	'readOneMissingToken'            => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/138c6cfc-ed49-476b-9f1e-6ee1dcb24f0b',
		null,
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/forbidden.json',
	],
	'readAllEmptyToken'              => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration',
		'',
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/forbidden.json',
	],
	'readOneEmptyToken'              => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/138c6cfc-ed49-476b-9f1e-6ee1dcb24f0b',
		'',
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/forbidden.json',
	],
	'readAllInvalidToken'            => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration',
		'Bearer ' . INVALID_TOKEN,
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
	'readOneInvalidToken'            => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/138c6cfc-ed49-476b-9f1e-6ee1dcb24f0b',
		'Bearer ' . INVALID_TOKEN,
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
	'readAllExpiredToken'            => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration',
		'Bearer ' . EXPIRED_TOKEN,
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
	'readOneExpiredToken'            => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/configuration/138c6cfc-ed49-476b-9f1e-6ee1dcb24f0b',
		'Bearer ' . EXPIRED_TOKEN,
		StatusCodeInterface::STATUS_UNAUTHORIZED,
		__DIR__ . '/responses/unauthorized.json',
	],
];
