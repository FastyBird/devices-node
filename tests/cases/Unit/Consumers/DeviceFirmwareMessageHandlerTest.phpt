<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\Consumers;
use FastyBird\NodeLibs\Publishers as NodeLibsPublishers;
use Mockery;
use Nette\Utils;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

final class DeviceFirmwareMessageHandlerTest extends DbTestCase
{

	/**
	 * @param string $routingKey
	 * @param Utils\ArrayHash $message
	 * @param mixed[] $fixture
	 *
	 * @dataProvider ./../../../fixtures/Consumers/deviceFirmwareMessage.php
	 */
	public function testProcessMessage(string $routingKey, Utils\ArrayHash $message, array $fixture): void
	{
		$rabbitPublisher = Mockery::mock(NodeLibsPublishers\RabbitMqPublisher::class);
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
			NodeLibsPublishers\IRabbitMqPublisher::class,
			$rabbitPublisher
		);

		/** @var Consumers\DeviceHardwareMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Consumers\DeviceFirmwareMessageHandler::class);

		$handler->process($routingKey, $message);
	}

}

$test_case = new DeviceFirmwareMessageHandlerTest();
$test_case->run();
