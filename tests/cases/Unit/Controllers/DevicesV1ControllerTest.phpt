<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\Connections;
use FastyBird\DevicesNode\Router;
use FastyBird\NodeExchange\Publishers as NodeExchangePublishers;
use FastyBird\NodeWebServer\Http;
use Fig\Http\Message\RequestMethodInterface;
use Mockery;
use PHPOnCouch;
use Ramsey\Uuid;
use React\Http\Io\ServerRequest;
use stdClass;
use Tester\Assert;
use Tests\Tools;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

/**
 * @testCase
 */
final class DevicesV1ControllerTest extends DbTestCase
{

	public function setUp(): void
	{
		parent::setUp();

		$doc = new stdClass();
		$doc->id = Uuid\Uuid::uuid4();

		$docs = [];
		$docs[] = $doc;

		$storageClient = Mockery::mock(PHPOnCouch\CouchClient::class);
		$storageClient
			->shouldReceive('asCouchDocuments')
			->getMock()
			->shouldReceive('find')
			->andReturn($docs)
			->getMock()
			->shouldReceive('storeDoc')
			->getMock();

		$storageConnection = Mockery::mock(Connections\CouchDbConnection::class);
		$storageConnection
			->shouldReceive('getClient')
			->andReturn($storageClient)
			->getMock();

		$this->mockContainerService(
			Connections\CouchDbConnection::class,
			$storageConnection
		);
	}

	/**
	 * @param string $url
	 * @param string|null $token
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/devicesRead.php
	 */
	public function testRead(string $url, ?string $token, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$headers = [];

		if ($token !== null) {
			$headers['authorization'] = $token;
		}

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_GET,
			$url,
			$headers
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
	 * @param string|null $token
	 * @param string $body
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/devicesCreate.php
	 */
	public function testCreate(string $url, ?string $token, string $body, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$headers = [];

		if ($token !== null) {
			$headers['authorization'] = $token;
		}

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_POST,
			$url,
			$headers,
			$body
		);

		$rabbitPublisher = Mockery::mock(NodeExchangePublishers\RabbitMqPublisher::class);
		$rabbitPublisher
			->shouldReceive('publish')
			->withArgs(function (string $routingKey, array $data): bool {
				Assert::same('fb.bus.node.entity.created.device', $routingKey);
				Assert::false($data === []);

				return true;
			});

		$this->mockContainerService(
			NodeExchangePublishers\IRabbitMqPublisher::class,
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
	 * @param string|null $token
	 * @param string $body
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/devicesUpdate.php
	 */
	public function testUpdate(string $url, ?string $token, string $body, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$headers = [];

		if ($token !== null) {
			$headers['authorization'] = $token;
		}

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_PATCH,
			$url,
			$headers,
			$body
		);

		$rabbitPublisher = Mockery::mock(NodeExchangePublishers\RabbitMqPublisher::class);
		$rabbitPublisher
			->shouldReceive('publish')
			->withArgs(function (string $routingKey, array $data): bool {
				Assert::same('fb.bus.node.entity.updated.device', $routingKey);
				Assert::false($data === []);

				return true;
			});

		$this->mockContainerService(
			NodeExchangePublishers\IRabbitMqPublisher::class,
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
	 * @param string|null $token
	 * @param int $statusCode
	 * @param string $fixture
	 *
	 * @dataProvider ./../../../fixtures/Controllers/devicesDelete.php
	 */
	public function testDelete(string $url, ?string $token, int $statusCode, string $fixture): void
	{
		/** @var Router\Router $router */
		$router = $this->getContainer()->getByType(Router\Router::class);

		$headers = [];

		if ($token !== null) {
			$headers['authorization'] = $token;
		}

		$request = new ServerRequest(
			RequestMethodInterface::METHOD_DELETE,
			$url,
			$headers
		);

		$rabbitPublisher = Mockery::mock(NodeExchangePublishers\RabbitMqPublisher::class);
		$rabbitPublisher
			->shouldReceive('publish')
			->withArgs(function (string $routingKey, array $data): bool {
				Assert::true(in_array($routingKey, [
					'fb.bus.node.entity.deleted.device',
					'fb.bus.node.entity.deleted.device.property',
					'fb.bus.node.entity.deleted.device.configuration',
					'fb.bus.node.entity.deleted.device.hardware',
					'fb.bus.node.entity.deleted.device.firmware',
					'fb.bus.node.entity.deleted.channel',
					'fb.bus.node.entity.deleted.channel.property',
					'fb.bus.node.entity.deleted.channel.configuration',
				], true));
				Assert::false($data === []);

				return true;
			});

		$this->mockContainerService(
			NodeExchangePublishers\IRabbitMqPublisher::class,
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
