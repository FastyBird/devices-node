<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\Connections;
use FastyBird\DevicesNode\Consumers;
use FastyBird\NodeExchange\Publishers as NodeExchangePublishers;
use Mockery;
use Nette\Utils;
use PHPOnCouch;
use Ramsey\Uuid;
use stdClass;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

/**
 * @testCase
 */
final class DeviceFirmwareMessageHandlerTest extends DbTestCase
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
			->andReturn($storageClient)
			->getMock()
			->shouldReceive('find')
			->andReturn($docs)
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
	 * @param string $routingKey
	 * @param Utils\ArrayHash $message
	 * @param mixed[] $fixture
	 *
	 * @dataProvider ./../../../fixtures/Consumers/deviceFirmwareMessage.php
	 */
	public function testProcessMessage(string $routingKey, Utils\ArrayHash $message, array $fixture): void
	{
		$rabbitPublisher = Mockery::mock(NodeExchangePublishers\RabbitMqPublisher::class);
		$rabbitPublisher
			->shouldReceive('publish')
			->withArgs(function (string $routingKey, array $data) use ($fixture): bool {
				if (Utils\Strings::contains($routingKey, 'created')) {
					unset($data['id']);
				}

				Assert::false($data === []);
				Assert::true(isset($fixture[$routingKey]));
				Assert::equal($fixture[$routingKey], $data);

				return true;
			})
			->times(count($fixture));

		$this->mockContainerService(
			NodeExchangePublishers\IRabbitMqPublisher::class,
			$rabbitPublisher
		);

		/** @var Consumers\DeviceHardwareMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Consumers\DeviceFirmwareMessageHandler::class);

		$handler->process($routingKey, $message);
	}

}

$test_case = new DeviceFirmwareMessageHandlerTest();
$test_case->run();
