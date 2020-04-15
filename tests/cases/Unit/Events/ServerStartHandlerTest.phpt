<?php declare(strict_types = 1);

namespace Tests\Cases;

use Doctrine\Common;
use Doctrine\DBAL;
use Doctrine\Persistence;
use FastyBird\DevicesNode\Events;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;

require_once __DIR__ . '/../../../bootstrap.php';

final class ServerStartHandlerTest extends BaseMockeryTestCase
{

	public function testServerStart(): void
	{
		$connection = Mockery::mock(DBAL\Connection::class);
		$connection
			->shouldReceive('ping')
			->withNoArgs()
			->andReturn(true)
			->times(1);

		$manager = Mockery::mock(Persistence\ObjectManager::class);
		$manager
			->shouldReceive('getConnection')
			->withNoArgs()
			->andReturn($connection)
			->times(1);

		$managerRegistry = Mockery::mock(Common\Persistence\ManagerRegistry::class);
		$managerRegistry
			->shouldReceive('getManager')
			->withNoArgs()
			->andReturn($manager)
			->times(2);

		$subscriber = new Events\ServerStartHandler($managerRegistry);

		$subscriber->__invoke();
	}

}

$test_case = new ServerStartHandlerTest();
$test_case->run();
