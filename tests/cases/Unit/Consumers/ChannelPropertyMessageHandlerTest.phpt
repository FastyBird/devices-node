<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\Consumers;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\States;
use FastyBird\NodeExchange\Publishers as NodeExchangePublishers;
use Mockery;
use Nette\Utils;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

/**
 * @testCase
 */
final class ChannelPropertyMessageHandlerTest extends DbTestCase
{

	public function setUp(): void
	{
		parent::setUp();

		$channelStateMock = Mockery::mock(States\Channels\IProperty::class);
		$channelStateMock
			->shouldReceive('getValue')
			->andReturn(null)
			->getMock()
			->shouldReceive('getExpected')
			->andReturn(null)
			->getMock()
			->shouldReceive('isPending')
			->andReturn(false)
			->getMock()
			->shouldReceive('toArray')
			->andReturn([
				'value'    => null,
				'expected' => null,
				'pending'  => false,
			])
			->getMock();

		$channelStatePropertyRepositoryMock = Mockery::mock(Models\States\Channels\PropertyRepository::class);
		$channelStatePropertyRepositoryMock
			->shouldReceive('findOne')
			->andReturn($channelStateMock)
			->getMock()
			->shouldReceive('findValue')
			->andReturn(null)
			->getMock()
			->shouldReceive('findExpected')
			->andReturn(null)
			->getMock();

		$this->mockContainerService(
			Models\States\Channels\PropertyRepository::class,
			$channelStatePropertyRepositoryMock
		);

		$channelStatePropertiesManagerMock = Mockery::mock(Models\States\Channels\PropertiesManager::class);
		$channelStatePropertiesManagerMock
			->shouldReceive('create')
			->andReturn($channelStateMock)
			->getMock()
			->shouldReceive('update')
			->andReturn($channelStateMock)
			->getMock()
			->shouldReceive('updateState')
			->andReturn($channelStateMock)
			->getMock()
			->shouldReceive('delete')
			->andReturn(true)
			->getMock();

		$this->mockContainerService(
			Models\States\Channels\PropertiesManager::class,
			$channelStatePropertiesManagerMock
		);
	}

	/**
	 * @param string $routingKey
	 * @param Utils\ArrayHash $message
	 * @param mixed[] $fixture
	 *
	 * @dataProvider ./../../../fixtures/Consumers/channelPropertyMessage.php
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

		/** @var Consumers\ChannelPropertyMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Consumers\ChannelPropertyMessageHandler::class);

		$handler->process($routingKey, '', $message);
	}

}

$test_case = new ChannelPropertyMessageHandlerTest();
$test_case->run();
