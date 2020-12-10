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
final class DeviceFirmwareMessageHandlerTest extends DbTestCase
{

	/**
	 * @param MqttPluginEntities\Firmware $entity
	 * @param Utils\ArrayHash $fixture
	 *
	 * @dataProvider ./../../../fixtures/Handlers/deviceFirmwareMessage.php
	 * @skip
	 */
	public function testProcessMessage(MqttPluginEntities\Firmware $entity, Utils\ArrayHash $fixture): void
	{
		$firmwareManager = Mockery::mock(DevicesModuleModels\Devices\PhysicalDevice\FirmwareManager::class);
		$firmwareManager
			->shouldReceive('update')
			->withArgs(function (
				DevicesModuleEntities\Devices\PhysicalDevice\IFirmware $firmware,
				Utils\ArrayHash $toUpdate
			) use ($fixture): bool {
				Assert::false($toUpdate === []);
				Assert::equal($fixture, $toUpdate);

				return true;
			})
			->times(1);

		$this->mockContainerService(
			DevicesModuleModels\Devices\PhysicalDevice\IFirmwareManager::class,
			$firmwareManager
		);

		/** @var Handlers\MQTT\DeviceFirmwareMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Handlers\MQTT\DeviceFirmwareMessageHandler::class);

		$handler->process($entity);
	}

}

$test_case = new DeviceFirmwareMessageHandlerTest();
$test_case->run();
