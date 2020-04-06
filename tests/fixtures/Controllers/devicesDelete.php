<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'delete'        => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa',
		StatusCodeInterface::STATUS_NO_CONTENT,
		__DIR__ . '/responses/devices.delete.json',
	],
	'deleteUnknown' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009af',
		StatusCodeInterface::STATUS_NOT_FOUND,
		__DIR__ . '/responses/devices.notFound.json',
	],
];
