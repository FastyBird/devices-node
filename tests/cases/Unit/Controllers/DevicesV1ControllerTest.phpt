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

	/**
	 * @param string $url
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/devicesRead.php
	 */
	public function testRead(string $url, int $statusCode, string $fixture): void
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
		Assert::same($statusCode, $response->getStatusCode());
		Assert::type(Http\Response::class, $response);
	}

	/**
	 * @param string $url
	 * @param string $body
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/devicesCreate.php
	 */
	public function testCreate(string $url, string $body, int $statusCode, string $fixture): void
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
				Assert::same('fb.bus.node.entity.created.device', $routingKey);
				Assert::false($data === []);

				return true;
			});

		$this->mockContainerService(
			NodeLibsPublishers\IRabbitMqPublisher::class,
			$rabbitPublisher
		);

		$response = $router->handle($request);

		Tools\JsonAssert::assertFixtureMatch(
			$fixture,
			(string) $response->getBody()
		);
		Assert::same($statusCode, $response->getStatusCode());
		Assert::type(Http\Response::class, $response);
	}

	/**
	 * @param string $url
	 * @param string $body
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/devicesUpdate.php
	 */
	public function testUpdate(string $url, string $body, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_PATCH,
			$url,
			[],
			$body
		);

		$rabbitPublisher = Mockery::mock(NodeLibsPublishers\RabbitMqPublisher::class);
		$rabbitPublisher
			->shouldReceive('publish')
			->withArgs(function (string $routingKey, array $data): bool {
				Assert::same('fb.bus.node.entity.updated.device', $routingKey);
				Assert::false($data === []);

				return true;
			});

		$this->mockContainerService(
			NodeLibsPublishers\IRabbitMqPublisher::class,
			$rabbitPublisher
		);

		$response = $router->handle($request);

		Tools\JsonAssert::assertFixtureMatch(
			$fixture,
			(string) $response->getBody()
		);
		Assert::same($statusCode, $response->getStatusCode());
		Assert::type(Http\Response::class, $response);
	}

	/**
	 * @param string $url
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/devicesDelete.php
	 */
	public function testDelete(string $url, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_DELETE,
			$url
		);

		$rabbitPublisher = Mockery::mock(NodeLibsPublishers\RabbitMqPublisher::class);
		$rabbitPublisher
			->shouldReceive('publish')
			->withArgs(function (string $routingKey, array $data): bool {
				Assert::true(in_array($routingKey, [
					'fb.bus.node.entity.deleted.device',
					'fb.bus.node.entity.deleted.device.property',
					'fb.bus.node.entity.deleted.device.hardware',
					'fb.bus.node.entity.deleted.device.firmware',
					'fb.bus.node.entity.deleted.device.channel',
					'fb.bus.node.entity.deleted.device.channel.property',
				], true));
				Assert::false($data === []);

				return true;
			});

		$this->mockContainerService(
			NodeLibsPublishers\IRabbitMqPublisher::class,
			$rabbitPublisher
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

$test_case = new DevicesV1ControllerTest();
$test_case->run();
