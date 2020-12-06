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
 */
final class ChannelControlMessageHandlerTest extends DbTestCase
{

	/**
	 * @param MqttPluginEntities\ChannelControl $entity
	 * @param Utils\ArrayHash $fixture
	 *
	 * @dataProvider ./../../../fixtures/Handlers/channelControlSchemaMessage.php
	 */
	public function XtestProcessSchemaMessage(MqttPluginEntities\ChannelControl $entity, Utils\ArrayHash $fixture): void
	{
		$rowsManager = Mockery::mock(DevicesModuleModels\Channels\Configuration\RowsManager::class);
		$rowsManager
			->shouldReceive('update')
			->withArgs(function (DevicesModuleEntities\Channels\Configuration\IRow $row, Utils\ArrayHash $toUpdate) use ($fixture): bool {
				Assert::false($toUpdate === []);
				Assert::equal($fixture, $toUpdate);

				return true;
			})
			->times(1);

		$this->mockContainerService(
			DevicesModuleModels\Channels\Configuration\IRowsManager::class,
			$rowsManager
		);

		/** @var Handlers\MQTT\ChannelControlMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Handlers\MQTT\ChannelControlMessageHandler::class);

		$handler->process($entity);
	}

	/**
	 * @param MqttPluginEntities\ChannelControl $entity
	 * @param Utils\ArrayHash $fixture
	 *
	 * @dataProvider ./../../../fixtures/Handlers/channelControlValueMessage.php
	 */
	public function XtestProcessValueMessage(MqttPluginEntities\ChannelControl $entity, Utils\ArrayHash $fixture): void
	{
		$rowsManager = Mockery::mock(DevicesModuleModels\Channels\Configuration\RowsManager::class);
		$rowsManager
			->shouldReceive('update')
			->withArgs(function (DevicesModuleEntities\Channels\Configuration\IRow $row, Utils\ArrayHash $toUpdate) use ($fixture): bool {
				Assert::false($toUpdate === []);
				Assert::equal($fixture, $toUpdate);

				return true;
			})
			->times(1);

		$this->mockContainerService(
			DevicesModuleModels\Channels\Configuration\IRowsManager::class,
			$rowsManager
		);

		/** @var Handlers\MQTT\ChannelControlMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Handlers\MQTT\ChannelControlMessageHandler::class);

		$handler->process($entity);
	}

}

$test_case = new ChannelControlMessageHandlerTest();
$test_case->run();
