<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\CouchDbStoragePlugin\Models as CouchDbStoragePluginModels;
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
final class DevicePropertyMessageHandlerTest extends DbTestCase
{

	/**
	 * @param MqttPluginEntities\DeviceProperty $entity
	 * @param Utils\ArrayHash $fixture
	 *
	 * @dataProvider ./../../../fixtures/Handlers/devicePropertyMessage.php
	 */
	public function XtestProcessMessage(MqttPluginEntities\DeviceProperty $entity, Utils\ArrayHash $fixture): void
	{
		$devicesPropertiesManager = Mockery::mock(DevicesModuleModels\Devices\Properties\PropertiesManager::class);
		$devicesPropertiesManager
			->shouldReceive('update')
			->withArgs(function (DevicesModuleEntities\IProperty $property, Utils\ArrayHash $toUpdate) use ($fixture): bool {
				Assert::false($toUpdate === []);
				Assert::equal($fixture, $toUpdate);

				return true;
			})
			->times(1);

		$this->mockContainerService(
			DevicesModuleModels\Devices\Properties\IPropertiesManager::class,
			$devicesPropertiesManager
		);

		$propertyStateRepository = Mockery::mock(CouchDbStoragePluginModels\PropertyRepository::class);

		$this->mockContainerService(
			CouchDbStoragePluginModels\IPropertyRepository::class,
			$propertyStateRepository
		);

		$propertiesStatesManager = Mockery::mock(CouchDbStoragePluginModels\PropertiesManager::class);

		$this->mockContainerService(
			CouchDbStoragePluginModels\IPropertiesManager::class,
			$propertiesStatesManager
		);

		/** @var Handlers\MQTT\DevicePropertyMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Handlers\MQTT\DevicePropertyMessageHandler::class);

		$handler->process($entity);
	}

}

$test_case = new DevicePropertyMessageHandlerTest();
$test_case->run();
