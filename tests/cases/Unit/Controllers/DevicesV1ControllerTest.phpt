<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\Router;
use FastyBird\NodeLibs\Publishers as NodeLibsPublishers;
use FastyBird\NodeWebServer\Http;
use Fig\Http\Message\RequestMethodInterface;
use Mockery;
use React\Http\Io\ServerRequest;
use Tester\Assert;
use Tests\Tools;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

final class DevicesV1ControllerTest extends DbTestCase
{

	public function setUp(): void
	{
		$this->registerDatabaseSchemaFile(__DIR__ . '/../../../sql/dummy.data.sql');

		parent::setUp();
	}

	/**
	 * @param string $url
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/devicesRead.php
	 */
	public function testRead(string $url, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_GET,
			$url
		);

		$response = $router->handle($request);

		Tools\JsonAssert::assertFixtureMatch(
			$fixture,
			(string) $response->getBody()
		);
		Assert::type(Http\Response::class, $response);
	}

	/**
	 * @param string $url
	 * @param string $body
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/devicesCreate.php
	 */
	public function testCreate(string $url, string $body, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_POST,
			$url,
			[],
			$body
		);

		$rabbitPublisher = Mockery::mock(NodeLibsPublishers\RabbitMqPublisher::class);
		$rabbitPublisher
			->shouldReceive('publish')
			->withArgs(function (string $routingKey, array $data): bool {
				return true;
			})
			->times(1);

		$this->mockContainerService(
			NodeLibsPublishers\IRabbitMqPublisher::class,
			$rabbitPublisher
		);

		$response = $router->handle($request);

		Tools\JsonAssert::assertFixtureMatch(
			$fixture,
			(string) $response->getBody()
		);
		Assert::type(Http\Response::class, $response);
	}

}

$test_case = new DevicesV1ControllerTest();
$test_case->run();
