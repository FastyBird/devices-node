<?php declare(strict_types = 1);

namespace Tests\Cases;

use Doctrine\Common;
use Doctrine\DBAL;
use Doctrine\Persistence;
use FastyBird\DevicesNode\Events;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;

require_once __DIR__ . '/../../../bootstrap.php';

final class RequestHandlerTest extends BaseMockeryTestCase
{

	public function testOnRequest(): void
	{
		$connection = Mockery::mock(DBAL\Connection::class);
		$connection
			->shouldReceive('ping')
			->withNoArgs()
			->andReturn(true)
			->times(1);

		$manager = Mockery::mock(Persistence\ObjectManager::class);
		$manager
			->shouldReceive('isOpen')
			->withNoArgs()
			->andReturn(true)
			->times(1)
			->getMock()
			->shouldReceive('getConnection')
			->withNoArgs()
			->andReturn($connection)
			->times(1)
			->getMock()
			->shouldReceive('clear')
			->withNoArgs()
			->times(1);

		$managerRegistry = Mockery::mock(Common\Persistence\ManagerRegistry::class);
		$managerRegistry
			->shouldReceive('getManager')
			->withNoArgs()
			->andReturn($manager)
			->times(2);

		$subscriber = new Events\RequestHandler($managerRegistry);

		$subscriber->__invoke();
	}

}

$test_case = new RequestHandlerTest();
$test_case->run();
