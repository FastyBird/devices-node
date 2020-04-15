<?php declare(strict_types = 1);

namespace Tests\Cases;

use Doctrine\Common;
use Doctrine\DBAL;
use Doctrine\ORM;
use FastyBird\DevicesNode\Events;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use Tester\Assert;

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

		$manager = Mockery::mock(ORM\EntityManagerInterface::class);
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
			->times(1);

		$subscriber = new Events\ServerStartHandler($managerRegistry);

		$subscriber->__invoke();

		Assert::true(true);
	}

}

$test_case = new ServerStartHandlerTest();
$test_case->run();
