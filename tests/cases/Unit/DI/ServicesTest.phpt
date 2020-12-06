<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\Bootstrap\Boot;
use FastyBird\DevicesNode\Commands;
use FastyBird\DevicesNode\Consumers;
use FastyBird\DevicesNode\Events;
use FastyBird\DevicesNode\Handlers;
use FastyBird\DevicesNode\Subscribers;
use Ninjify\Nunjuck\TestCase\BaseTestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class ServicesTest extends BaseTestCase
{

	public function testServicesRegistration(): void
	{
		$configurator = Boot\Bootstrap::boot();
		$configurator->addParameters([
			'database' => [
				'driver' => 'pdo_sqlite',
			],
		]);

		$container = $configurator->createContainer();

		Assert::notNull($container->getByType(Commands\InitializeCommand::class));

		Assert::notNull($container->getByType(Consumers\DevicePropertyMessageHandler::class));
		Assert::notNull($container->getByType(Consumers\ChannelPropertyMessageHandler::class));

		Assert::notNull($container->getByType(Events\ServerBeforeStartHandler::class));
		Assert::notNull($container->getByType(Events\PropertyStateUpdatedHandler::class));
		Assert::notNull($container->getByType(Events\MqttMessageHandler::class));
		Assert::notNull($container->getByType(Events\AfterConsumeHandler::class));

		Assert::notNull($container->getByType(Subscribers\EntitiesSubscriber::class));

		Assert::notNull($container->getByType(Handlers\MQTT\DeviceMessageHandler::class));
		Assert::notNull($container->getByType(Handlers\MQTT\DeviceFirmwareMessageHandler::class));
		Assert::notNull($container->getByType(Handlers\MQTT\DeviceHardwareMessageHandler::class));
		Assert::notNull($container->getByType(Handlers\MQTT\DevicePropertyMessageHandler::class));
		Assert::notNull($container->getByType(Handlers\MQTT\DeviceControlMessageHandler::class));
		Assert::notNull($container->getByType(Handlers\MQTT\ChannelMessageHandler::class));
		Assert::notNull($container->getByType(Handlers\MQTT\ChannelPropertyMessageHandler::class));
		Assert::notNull($container->getByType(Handlers\MQTT\ChannelControlMessageHandler::class));
	}

}

$test_case = new ServicesTest();
$test_case->run();
