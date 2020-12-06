<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\CouchDbStoragePlugin\Models as CouchDbStoragePluginModels;
use FastyBird\CouchDbStoragePlugin\States as CouchDbStoragePluginStates;
use FastyBird\DevicesNode\Consumers;
use FastyBird\MqttPlugin\Senders as MqttPluginSenders;
use Mockery;
use React;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

/**
 * @testCase
 */
final class DevicePropertyMessageHandlerTest extends DbTestCase
{

	/**
	 * @param string $routingKey
	 * @param string $origin
	 * @param string $payload
	 * @param mixed[] $state
	 *
	 * @dataProvider ./../../../fixtures/Consumers/devicePropertyMessage.php
	 */
	public function testProcessMessage(string $routingKey, string $origin, string $payload, array $state): void
	{
		$deferred = new React\Promise\Deferred();

		$stateMock = Mockery::mock(CouchDbStoragePluginStates\IProperty::class);
		$stateMock
			->shouldReceive('getValue')
			->andReturn(isset($state['value']) ? $state['value'] : null)
			->getMock()
			->shouldReceive('getExpected')
			->andReturn(isset($state['expected']) ? $state['expected'] : null)
			->getMock()
			->shouldReceive('isPending')
			->andReturn(isset($state['pending']) ? $state['pending'] : false)
			->getMock()
			->shouldReceive('toArray')
			->andReturn($state)
			->getMock();

		$this->mockStateManagement($stateMock);

		$sender = Mockery::mock(MqttPluginSenders\MqttV1Sender::class);
		$sender
			->shouldReceive('sendDeviceProperty')
			->withArgs(function ($device, $property, $payload, $parent) use ($deferred): bool {
				return true;
			})
			->andReturn($deferred->promise());

		$this->mockContainerService(
			MqttPluginSenders\MqttV1Sender::class,
			$sender
		);

		/** @var Consumers\DevicePropertyMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Consumers\DevicePropertyMessageHandler::class);

		Assert::true($handler->process($routingKey, $origin, $payload));
	}

	private function mockStateManagement(CouchDbStoragePluginStates\IProperty $stateMock): void
	{
		$statePropertyRepositoryMock = Mockery::mock(CouchDbStoragePluginModels\PropertyRepository::class);
		$statePropertyRepositoryMock
			->shouldReceive('findOne')
			->andReturn($stateMock)
			->getMock()
			->shouldReceive('findValue')
			->andReturn(null)
			->getMock()
			->shouldReceive('findExpected')
			->andReturn(null)
			->getMock();

		$this->mockContainerService(
			CouchDbStoragePluginModels\PropertyRepository::class,
			$statePropertyRepositoryMock
		);

		$statePropertiesManagerMock = Mockery::mock(CouchDbStoragePluginModels\PropertiesManager::class);
		$statePropertiesManagerMock
			->shouldReceive('updateState')
			->andReturn($stateMock)
			->times(1)
			->getMock();

		$this->mockContainerService(
			CouchDbStoragePluginModels\PropertiesManager::class,
			$statePropertiesManagerMock
		);
	}

}

$test_case = new DevicePropertyMessageHandlerTest();
$test_case->run();
