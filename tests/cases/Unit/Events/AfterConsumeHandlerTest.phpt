<?php declare(strict_types = 1);

namespace Tests\Cases;

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\DevicesNode\Events;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

final class AfterConsumeHandlerTest extends BaseMockeryTestCase
{

	public function testOnConsumedMessage(): void
	{
		$manager = Mockery::mock(ORM\EntityManagerInterface::class);
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
			->times(1);

		$subscriber = new Events\AfterConsumeHandler($managerRegistry);

		$subscriber->__invoke();

		Assert::true(true);
	}

}

$test_case = new AfterConsumeHandlerTest();
$test_case->run();
