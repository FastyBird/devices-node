<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\Router;
use FastyBird\NodeWebServer\Http;
use Fig\Http\Message\RequestMethodInterface;
use React\Http\Io\ServerRequest;
use Tester\Assert;
use Tests\Tools;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

final class AuthenticateV1ControllerTest extends DbTestCase
{

	/**
	 * @param string $url
	 * @param string $body
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/authenticateVerneMQ.php
	 */
	public function testAuthenticateVerneMq(string $url, string $body, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_POST,
			$url,
			[],
			$body
		);

		$response = $router->handle($request);

		Tools\JsonAssert::assertFixtureMatch(
			$fixture,
			(string) $response->getBody()
		);
		Assert::same($statusCode, $response->getStatusCode());
		Assert::type(Http\Response::class, $response);
	}

}

$test_case = new AuthenticateV1ControllerTest();
$test_case->run();
