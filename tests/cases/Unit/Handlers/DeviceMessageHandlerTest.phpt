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
final class DeviceMessageHandlerTest extends DbTestCase
{

	/**
	 * @param MqttPluginEntities\DeviceAttribute $entity
	 * @param Utils\ArrayHash $fixture
	 *
	 * @dataProvider ./../../../fixtures/Handlers/deviceMessage.php
	 * @skip
	 */
	public function testProcessMessage(MqttPluginEntities\DeviceAttribute $entity, Utils\ArrayHash $fixture): void
	{
		$devicesManager = Mockery::mock(DevicesModuleModels\Devices\DevicesManager::class);
		$devicesManager
			->shouldReceive('update')
			->withArgs(function (DevicesModuleEntities\Devices\IDevice $device, Utils\ArrayHash $toUpdate) use ($fixture): bool {
				Assert::false($toUpdate === []);
				Assert::equal($fixture, $toUpdate);

				return true;
			})
			->times(1);

		$this->mockContainerService(
			DevicesModuleModels\Devices\IDevicesManager::class,
			$devicesManager
		);

		$devicesPropertiesManager = Mockery::mock(DevicesModuleModels\Devices\Properties\PropertiesManager::class);

		$this->mockContainerService(
			DevicesModuleModels\Devices\Properties\IPropertiesManager::class,
			$devicesPropertiesManager
		);

		$devicesControlsManager = Mockery::mock(DevicesModuleModels\Devices\Controls\ControlsManager::class);

		$this->mockContainerService(
			DevicesModuleModels\Devices\Controls\IControlsManager::class,
			$devicesControlsManager
		);

		/** @var Handlers\MQTT\DeviceMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Handlers\MQTT\DeviceMessageHandler::class);

		$handler->process($entity);
	}

}

$test_case = new DeviceMessageHandlerTest();
$test_case->run();
