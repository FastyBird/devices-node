<?php declare(strict_types = 1);

namespace Tests\Cases;

use Doctrine\Common;
use Doctrine\ORM;
use Doctrine\Persistence;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Subscribers;
use FastyBird\NodeLibs\Publishers as NodeLibsPublishers;
use FastyBird\NodeWebServer\Events;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use stdClass;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

final class HttpServerSubscriberTest extends BaseMockeryTestCase
{

	public function testSubscriberEvents(): void
	{
		$managerRegistry = Mockery::mock(Common\Persistence\ManagerRegistry::class);

		$subscriber = new Subscribers\HttpServerSubscriber($managerRegistry);

		Assert::same([
			Events\ResponseEvent::class        => 'onResponse',
			Events\ConsumerMessageEvent::class => 'onConsumedMessage',
		], $subscriber->getSubscribedEvents());
	}

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

		$subscriber = new Subscribers\HttpServerSubscriber($managerRegistry);

		$subscriber->onResponse();
	}

	public function testOnConsumedMessage(): void
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

		$subscriber = new Subscribers\HttpServerSubscriber($managerRegistry);

		$subscriber->onConsumedMessage();
	}

}

$test_case = new HttpServerSubscriberTest();
$test_case->run();
