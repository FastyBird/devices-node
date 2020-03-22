<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\Commands;
use FastyBird\DevicesNode\Consumers;
use FastyBird\DevicesNode\Controllers;
use FastyBird\DevicesNode\Hydrators;
use FastyBird\DevicesNode\Middleware;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Schemas;
use FastyBird\DevicesNode\Subscribers;
use FastyBird\NodeLibs\Boot;
use Ninjify\Nunjuck\TestCase\BaseTestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

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

		Assert::notNull($container->getByType(Middleware\JsonApiMiddleware::class));

		Assert::notNull($container->getByType(Commands\Devices\CreateCommand::class));

		Assert::notNull($container->getByType(Consumers\DeviceMessageHandler::class));
		Assert::notNull($container->getByType(Consumers\DeviceFirmwareMessageHandler::class));
		Assert::notNull($container->getByType(Consumers\DeviceHardwareMessageHandler::class));
		Assert::notNull($container->getByType(Consumers\DevicePropertyMessageHandler::class));
		Assert::notNull($container->getByType(Consumers\ChannelMessageHandler::class));
		Assert::notNull($container->getByType(Consumers\ChannelPropertyMessageHandler::class));

		Assert::notNull($container->getByType(Subscribers\HttpServerSubscriber::class));
		Assert::notNull($container->getByType(Subscribers\EntitiesSubscriber::class));

		Assert::notNull($container->getByType(Models\Devices\DeviceRepository::class));
		Assert::notNull($container->getByType(Models\Devices\Properties\PropertyRepository::class));
		Assert::notNull($container->getByType(Models\Channels\ChannelRepository::class));
		Assert::notNull($container->getByType(Models\Channels\Properties\PropertyRepository::class));

		Assert::notNull($container->getByType(Models\Devices\DevicesManager::class));
		Assert::notNull($container->getByType(Models\Devices\Controls\ControlsManager::class));
		Assert::notNull($container->getByType(Models\Devices\Properties\PropertiesManager::class));
		Assert::notNull($container->getByType(Models\Devices\Credentials\CredentialsManager::class));
		Assert::notNull($container->getByType(Models\Devices\PhysicalDevice\HardwareManager::class));
		Assert::notNull($container->getByType(Models\Devices\PhysicalDevice\FirmwareManager::class));
		Assert::notNull($container->getByType(Models\Channels\ChannelsManager::class));
		Assert::notNull($container->getByType(Models\Channels\Controls\ControlsManager::class));
		Assert::notNull($container->getByType(Models\Channels\Properties\PropertiesManager::class));
		Assert::notNull($container->getByType(Models\Channels\Configuration\RowsManager::class));

		Assert::notNull($container->getByType(Controllers\DevicesV1Controller::class));
		Assert::notNull($container->getByType(Controllers\DeviceChildrenV1Controller::class));
		Assert::notNull($container->getByType(Controllers\DevicePropertiesV1Controller::class));
		Assert::notNull($container->getByType(Controllers\DeviceConfigurationV1Controller::class));
		Assert::notNull($container->getByType(Controllers\DeviceCredentialsV1Controller::class));
		Assert::notNull($container->getByType(Controllers\DeviceHardwareV1Controller::class));
		Assert::notNull($container->getByType(Controllers\DeviceFirmwareV1Controller::class));
		Assert::notNull($container->getByType(Controllers\ChannelsV1Controller::class));
		Assert::notNull($container->getByType(Controllers\ChannelPropertiesV1Controller::class));
		Assert::notNull($container->getByType(Controllers\ChannelConfigurationV1Controller::class));

		Assert::notNull($container->getByType(Schemas\Devices\PhysicalDeviceSchema::class));
		Assert::notNull($container->getByType(Schemas\Devices\Properties\PropertySchema::class));
		Assert::notNull($container->getByType(Schemas\Devices\Hardware\HardwareSchema::class));
		Assert::notNull($container->getByType(Schemas\Devices\Firmware\FirmwareSchema::class));
		Assert::notNull($container->getByType(Schemas\Devices\Credentials\CredentialsSchema::class));
		Assert::notNull($container->getByType(Schemas\Devices\Configuration\BooleanRowSchema::class));
		Assert::notNull($container->getByType(Schemas\Devices\Configuration\NumberRowSchema::class));
		Assert::notNull($container->getByType(Schemas\Devices\Configuration\SelectRowSchema::class));
		Assert::notNull($container->getByType(Schemas\Channels\ChannelSchema::class));
		Assert::notNull($container->getByType(Schemas\Channels\Properties\PropertySchema::class));
		Assert::notNull($container->getByType(Schemas\Channels\Configuration\BooleanRowSchema::class));
		Assert::notNull($container->getByType(Schemas\Channels\Configuration\NumberRowSchema::class));
		Assert::notNull($container->getByType(Schemas\Channels\Configuration\SelectRowSchema::class));
		Assert::notNull($container->getByType(Schemas\Channels\Configuration\TextRowSchema::class));

		Assert::notNull($container->getByType(Hydrators\Devices\PhysicalDeviceHydrator::class));
		Assert::notNull($container->getByType(Hydrators\Channels\ChannelHydrator::class));
	}

}

$test_case = new ServicesTest();
$test_case->run();
