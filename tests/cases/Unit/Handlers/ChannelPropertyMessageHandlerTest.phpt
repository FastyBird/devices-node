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
final class ChannelPropertyMessageHandlerTest extends DbTestCase
{

	/**
	 * @param MqttPluginEntities\ChannelProperty $entity
	 * @param Utils\ArrayHash $fixture
	 *
	 * @dataProvider ./../../../fixtures/Handlers/channelPropertyMessage.php
	 */
	public function XtestProcessMessage(MqttPluginEntities\ChannelProperty $entity, Utils\ArrayHash $fixture): void
	{
		$channelsPropertiesManager = Mockery::mock(DevicesModuleModels\Channels\Properties\PropertiesManager::class);
		$channelsPropertiesManager
			->shouldReceive('update')
			->withArgs(function (DevicesModuleEntities\IProperty $property, Utils\ArrayHash $toUpdate) use ($fixture): bool {
				Assert::false($toUpdate === []);
				Assert::equal($fixture, $toUpdate);

				return true;
			})
			->times(1);

		$this->mockContainerService(
			DevicesModuleModels\Channels\Properties\IPropertiesManager::class,
			$channelsPropertiesManager
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

		/** @var Handlers\MQTT\ChannelPropertyMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Handlers\MQTT\ChannelPropertyMessageHandler::class);

		$handler->process($entity);
	}

}

$test_case = new ChannelPropertyMessageHandlerTest();
$test_case->run();
