<?php declare(strict_types = 1);

namespace Tests\Cases;

use Doctrine\Common;
use Doctrine\Persistence;
use FastyBird\DevicesNode\Events;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;

require_once __DIR__ . '/../../../bootstrap.php';

final class ResponseHandlerTest extends BaseMockeryTestCase
{

	public function testOnResponse(): void
	{
		$manager = Mockery::mock(Persistence\ObjectManager::class);
		$manager
			->shouldReceive('flush')
			->withNoArgs()
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

		$subscriber = new Events\ResponseHandler($managerRegistry);

		$subscriber->__invoke();
	}

}

$test_case = new ResponseHandlerTest();
$test_case->run();
