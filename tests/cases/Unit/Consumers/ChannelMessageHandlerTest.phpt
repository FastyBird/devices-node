<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\Consumers;
use FastyBird\NodeExchange\Publishers as NodeExchangePublishers;
use Mockery;
use Nette\Utils;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

/**
 * @testCase
 */
final class ChannelMessageHandlerTest extends DbTestCase
{

	/**
	 * @param string $routingKey
	 * @param Utils\ArrayHash $message
	 * @param mixed[] $fixture
	 *
	 * @dataProvider ./../../../fixtures/Consumers/channelMessage.php
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

		/** @var Consumers\ChannelMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Consumers\ChannelMessageHandler::class);

		$handler->process($routingKey, $message);
	}

}

$test_case = new ChannelMessageHandlerTest();
$test_case->run();
