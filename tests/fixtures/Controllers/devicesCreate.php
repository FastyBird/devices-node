<?php declare(strict_types = 1);

return [
	'readAll' => [
		'/v1/devices',
		file_get_contents(__DIR__ . '/requests/devices.create.json'),
		__DIR__ . '/responses/devices.create.json',
	],
];
