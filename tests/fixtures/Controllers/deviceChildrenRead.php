<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'readAll' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/children',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.children.index.json',
	],
	'readAllPaging' => [
		'/v1/devices/69786d15-fd0c-4d9f-9378-33287c2009fa/children?page[offset]=1&page[limit]=1',
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/device.children.index.paging.json',
	],
];
