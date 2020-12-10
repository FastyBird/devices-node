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
final class DeviceHardwareMessageHandlerTest extends DbTestCase
{

	/**
	 * @param MqttPluginEntities\Hardware $entity
	 * @param Utils\ArrayHash $fixture
	 *
	 * @dataProvider ./../../../fixtures/Handlers/deviceHardwareMessage.php
	 * @skip
	 */
	public function testProcessMessage(MqttPluginEntities\Hardware $entity, Utils\ArrayHash $fixture): void
	{
		$hardwareManager = Mockery::mock(DevicesModuleModels\Devices\PhysicalDevice\HardwareManager::class);
		$hardwareManager
			->shouldReceive('update')
			->withArgs(function (
				DevicesModuleEntities\Devices\PhysicalDevice\IHardware $hardware,
				Utils\ArrayHash $toUpdate
			) use ($fixture): bool {
				Assert::false($toUpdate === []);
				Assert::equal($fixture, $toUpdate);

				return true;
			})
			->times(1);

		$this->mockContainerService(
			DevicesModuleModels\Devices\PhysicalDevice\IHardwareManager::class,
			$hardwareManager
		);

		/** @var Handlers\MQTT\DeviceHardwareMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Handlers\MQTT\DeviceHardwareMessageHandler::class);

		$handler->process($entity);
	}

}

$test_case = new DeviceHardwareMessageHandlerTest();
$test_case->run();
