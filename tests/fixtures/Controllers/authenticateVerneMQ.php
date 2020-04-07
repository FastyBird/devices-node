<?php declare(strict_types = 1);

use Fig\Http\Message\StatusCodeInterface;

return [
	'authenticate'    => [
		'/v1/authenticate/vernemq',
		file_get_contents(__DIR__ . '/requests/authenticate.vernemq.json'),
		StatusCodeInterface::STATUS_OK,
		__DIR__ . '/responses/authenticate.vernemq.json',
	],
	'missingRequired' => [
		'/v1/authenticate/vernemq',
		file_get_contents(__DIR__ . '/requests/authenticate.vernemq.missing.required.json'),
		StatusCodeInterface::STATUS_FORBIDDEN,
		__DIR__ . '/responses/authenticate.vernemq.missing.required.json',
	],
];
