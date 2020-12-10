<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesNode\Handlers;
use FastyBird\MqttPlugin\Entities as MqttPluginEntities;
use Mockery;
use Nette\Utils;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

/**
 * @testCase
 * @skip
 */
final class DeviceControlMessageHandlerTest extends DbTestCase
{

	/**
	 * @param MqttPluginEntities\DeviceControl $entity
	 * @param Utils\ArrayHash $fixture
	 *
	 * @dataProvider ./../../../fixtures/Handlers/deviceControlSchemaMessage.php
	 * @skip
	 */
	public function testProcessSchemaMessage(MqttPluginEntities\DeviceControl $entity, Utils\ArrayHash $fixture): void
	{
		$rowsManager = Mockery::mock(DevicesModuleModels\Devices\Configuration\RowsManager::class);
		$rowsManager
			->shouldReceive('update')
			->withArgs(function (DevicesModuleEntities\Devices\Configuration\IRow $row, Utils\ArrayHash $toUpdate) use (
				$fixture
			): bool {
				Assert::false($toUpdate === []);
				Assert::equal($fixture, $toUpdate);

				return true;
			})
			->times(1);

		$this->mockContainerService(
			DevicesModuleModels\Devices\Configuration\IRowsManager::class,
			$rowsManager
		);

		/** @var Handlers\MQTT\DeviceControlMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Handlers\MQTT\DeviceControlMessageHandler::class);

		$handler->process($entity);
	}

	/**
	 * @param MqttPluginEntities\DeviceControl $entity
	 * @param Utils\ArrayHash $fixture
	 *
	 * @dataProvider ./../../../fixtures/Handlers/deviceControlValueMessage.php
	 * @skip
	 */
	public function testProcessValueMessage(MqttPluginEntities\DeviceControl $entity, Utils\ArrayHash $fixture): void
	{
		$rowsManager = Mockery::mock(DevicesModuleModels\Devices\Configuration\RowsManager::class);
		$rowsManager
			->shouldReceive('update')
			->withArgs(function (DevicesModuleEntities\Devices\Configuration\IRow $row, Utils\ArrayHash $toUpdate) use (
				$fixture
			): bool {
				Assert::false($toUpdate === []);
				Assert::equal($fixture, $toUpdate);

				return true;
			})
			->times(1);

		$this->mockContainerService(
			DevicesModuleModels\Devices\Configuration\IRowsManager::class,
			$rowsManager
		);

		/** @var Handlers\MQTT\DeviceControlMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Handlers\MQTT\DeviceControlMessageHandler::class);

		$handler->process($entity);
	}

}

$test_case = new DeviceControlMessageHandlerTest();
$test_case->run();
