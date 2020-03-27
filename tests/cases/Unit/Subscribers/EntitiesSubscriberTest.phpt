<?php declare(strict_types = 1);

namespace Tests\Cases;

use Doctrine\ORM;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Subscribers;
use FastyBird\NodeLibs\Publishers as NodeLibsPublishers;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use stdClass;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

final class EntitiesSubscriberTest extends BaseMockeryTestCase
{

	public function testSubscriberEvents(): void
	{
		$publisher = Mockery::mock(NodeLibsPublishers\IRabbitMqPublisher::class);
		$entityManager = Mockery::mock(ORM\EntityManagerInterface::class);

		$subscriber = new Subscribers\EntitiesSubscriber($publisher, $entityManager);

		Assert::same(['onFlush', 'postPersist', 'postUpdate'], $subscriber->getSubscribedEvents());
	}

	public function testPublishCreatedEntity(): void
	{
		$publisher = Mockery::mock(NodeLibsPublishers\IRabbitMqPublisher::class);
		$publisher
			->shouldReceive('publish')
			->withArgs(function (string $key, array $data): bool {
				unset($data['id']);

				Assert::same('fb.bus.node.entity.created.device', $key);
				Assert::same([
					'identifier' => 'device-name',
					'name'       => 'Device custom name',
					'title'      => null,
					'comment'    => null,
					'state'      => 'unknown',
					'enabled'    => true,
					'control'    => [],
					'params'     => [],
				], $data);

				return true;
			})
			->times(1);

		$entityManager = $this->getEntityManager();

		$subscriber = new Subscribers\EntitiesSubscriber($publisher, $entityManager);

		$entity = new Entities\Devices\PhysicalDevice('device-name');
		$entity->setName('Device custom name');

		$eventArgs = Mockery::mock(ORM\Event\LifecycleEventArgs::class);
		$eventArgs
			->shouldReceive('getEntity')
			->withNoArgs()
			->andReturn($entity)
			->times(1);

		$subscriber->postPersist($eventArgs);
	}

	public function testPublishUpdatedEntity(): void
	{
		$publisher = Mockery::mock(NodeLibsPublishers\IRabbitMqPublisher::class);
		$publisher
			->shouldReceive('publish')
			->withArgs(function (string $key, array $data): bool {
				unset($data['id']);

				Assert::same('fb.bus.node.entity.updated.device', $key);
				Assert::same([
					'identifier' => 'device-name',
					'name'       => 'Device custom name',
					'title'      => null,
					'comment'    => null,
					'state'      => 'unknown',
					'enabled'    => true,
					'control'    => [],
					'params'     => [],
				], $data);

				return true;
			})
			->times(1);

		$entityManager = $this->getEntityManager(true);

		$subscriber = new Subscribers\EntitiesSubscriber($publisher, $entityManager);

		$entity = new Entities\Devices\PhysicalDevice('device-name');
		$entity->setName('Device custom name');

		$eventArgs = Mockery::mock(ORM\Event\LifecycleEventArgs::class);
		$eventArgs
			->shouldReceive('getEntity')
			->andReturn($entity)
			->times(1);

		$subscriber->postUpdate($eventArgs);
	}

	public function testPublishDeletedEntity(): void
	{
		$publisher = Mockery::mock(NodeLibsPublishers\IRabbitMqPublisher::class);
		$publisher
			->shouldReceive('publish')
			->withArgs(function (string $key, array $data): bool {
				unset($data['id']);

				Assert::same('fb.bus.node.entity.deleted.device', $key);
				Assert::same([
					'identifier' => 'device-name',
					'name'       => 'Device custom name',
					'title'      => null,
					'comment'    => null,
					'state'      => 'unknown',
					'enabled'    => true,
					'control'    => [],
					'params'     => [],
				], $data);

				return true;
			})
			->times(1);

		$entity = new Entities\Devices\PhysicalDevice('device-name');
		$entity->setName('Device custom name');

		$uow = Mockery::mock(ORM\UnitOfWork::class);
		$uow
			->shouldReceive('getScheduledEntityDeletions')
			->withNoArgs()
			->andReturn([$entity])
			->times(1)
			->getMock()
			->shouldReceive('getEntityIdentifier')
			->andReturn([
				123,
			])
			->times(1);

		$entityManager = $this->getEntityManager();
		$entityManager
			->shouldReceive('getUnitOfWork')
			->withNoArgs()
			->andReturn($uow)
			->times(1);

		$subscriber = new Subscribers\EntitiesSubscriber($publisher, $entityManager);

		$subscriber->onFlush();
	}

	/**
	 * @param bool $withUow
	 *
	 * @return ORM\EntityManagerInterface
	 */
	private function getEntityManager(bool $withUow = false): ORM\EntityManagerInterface
	{
		$metadata = new stdClass();
		$metadata->fieldMappings = [
			[
				'fieldName' => 'identifier',
			],
			[
				'fieldName' => 'name',
			],
			[
				'fieldName' => 'title',
			],
		];

		$entityManager = Mockery::mock(ORM\EntityManagerInterface::class);
		$entityManager
			->shouldReceive('getClassMetadata')
			->withArgs([Entities\Devices\PhysicalDevice::class])
			->andReturn($metadata);

		if ($withUow) {
			$uow = Mockery::mock(ORM\UnitOfWork::class);
			$uow
				->shouldReceive('getEntityChangeSet')
				->andReturn(['name'])
				->times(1);

			$entityManager
				->shouldReceive('getUnitOfWork')
				->withNoArgs()
				->andReturn($uow)
				->times(1);
		}

		return $entityManager;
	}

}

$test_case = new EntitiesSubscriberTest();
$test_case->run();
