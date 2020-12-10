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
final class ChannelMessageHandlerTest extends DbTestCase
{

	/**
	 * @param MqttPluginEntities\ChannelAttribute $entity
	 * @param Utils\ArrayHash $fixture
	 *
	 * @dataProvider ./../../../fixtures/Handlers/channelMessage.php
	 * @skip
	 */
	public function testProcessMessage(MqttPluginEntities\ChannelAttribute $entity, Utils\ArrayHash $fixture): void
	{
		$channelsManager = Mockery::mock(DevicesModuleModels\Channels\ChannelsManager::class);
		$channelsManager
			->shouldReceive('update')
			->withArgs(function (DevicesModuleEntities\Channels\IChannel $channel, Utils\ArrayHash $toUpdate) use (
				$fixture
			): bool {
				Assert::false($toUpdate === []);
				Assert::equal($fixture, $toUpdate);

				return true;
			})
			->times(1);

		$this->mockContainerService(
			DevicesModuleModels\Channels\IChannelsManager::class,
			$channelsManager
		);

		$channelsPropertiesManager = Mockery::mock(DevicesModuleModels\Channels\Properties\PropertiesManager::class);

		$this->mockContainerService(
			DevicesModuleModels\Channels\Properties\IPropertiesManager::class,
			$channelsPropertiesManager
		);

		$channelsControlsManager = Mockery::mock(DevicesModuleModels\Channels\Controls\ControlsManager::class);

		$this->mockContainerService(
			DevicesModuleModels\Channels\Controls\IControlsManager::class,
			$channelsControlsManager
		);

		/** @var Handlers\MQTT\ChannelMessageHandler $handler */
		$handler = $this->getContainer()->getByType(Handlers\MQTT\ChannelMessageHandler::class);

		$handler->process($entity);
	}

}

$test_case = new ChannelMessageHandlerTest();
$test_case->run();
