<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\Bootstrap\Boot;
use FastyBird\DevicesNode\Commands;
use FastyBird\DevicesNode\Consumers;
use FastyBird\DevicesNode\Events;
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

		Assert::notNull($container->getByType(Consumers\MQTT\DeviceMessageHandler::class));
		Assert::notNull($container->getByType(Consumers\MQTT\DeviceFirmwareMessageHandler::class));
		Assert::notNull($container->getByType(Consumers\MQTT\DeviceHardwareMessageHandler::class));
		Assert::notNull($container->getByType(Consumers\MQTT\DevicePropertyMessageHandler::class));
		Assert::notNull($container->getByType(Consumers\MQTT\DeviceControlMessageHandler::class));
		Assert::notNull($container->getByType(Consumers\MQTT\ChannelMessageHandler::class));
		Assert::notNull($container->getByType(Consumers\MQTT\ChannelPropertyMessageHandler::class));
		Assert::notNull($container->getByType(Consumers\MQTT\ChannelControlMessageHandler::class));

		Assert::notNull($container->getByType(Events\PropertyStateUpdatedHandler::class));
	}

}

$test_case = new ServicesTest();
$test_case->run();
