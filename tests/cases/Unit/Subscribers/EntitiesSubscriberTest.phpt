<?php declare(strict_types = 1);

namespace Tests\Cases;

use Doctrine\ORM;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Subscribers;
use FastyBird\NodeExchange\Publishers as NodeExchangePublishers;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use stdClass;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class EntitiesSubscriberTest extends BaseMockeryTestCase
{

	public function testSubscriberEvents(): void
	{
		$publisher = Mockery::mock(NodeExchangePublishers\IRabbitMqPublisher::class);
		$entityManager = Mockery::mock(ORM\EntityManagerInterface::class);

		$devicesPropertiesStatesManager = Mockery::mock(Models\States\Devices\PropertiesManager::class);
		$devicesPropertyStateRepository = Mockery::mock(Models\States\Devices\PropertyRepository::class);
		$channelsPropertiesStatesManager = Mockery::mock(Models\States\Channels\PropertiesManager::class);
		$channelPropertyStateRepository = Mockery::mock(Models\States\Channels\PropertyRepository::class);

		$subscriber = new Subscribers\EntitiesSubscriber(
			$devicesPropertiesStatesManager,
			$devicesPropertyStateRepository,
			$channelsPropertiesStatesManager,
			$channelPropertyStateRepository,
			$publisher,
			$entityManager
		);

		Assert::same(['preFlush', 'onFlush', 'postPersist', 'postUpdate'], $subscriber->getSubscribedEvents());
	}

	public function testPublishCreatedEntity(): void
	{
		$publisher = Mockery::mock(NodeExchangePublishers\IRabbitMqPublisher::class);
		$publisher
			->shouldReceive('publish')
			->withArgs(function (string $key, array $data): bool {
				unset($data['id']);

				Assert::same('fb.bus.node.entity.created.device', $key);
				Assert::equal([
					'identifier' => 'device-name',
					'type'       => 'physical',
					'parent'     => null,
					'device'     => 'device-name',
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

		$devicesPropertiesStatesManager = Mockery::mock(Models\States\Devices\PropertiesManager::class);
		$devicesPropertyStateRepository = Mockery::mock(Models\States\Devices\PropertyRepository::class);
		$channelsPropertiesStatesManager = Mockery::mock(Models\States\Channels\PropertiesManager::class);
		$channelPropertyStateRepository = Mockery::mock(Models\States\Channels\PropertyRepository::class);

		$subscriber = new Subscribers\EntitiesSubscriber(
			$devicesPropertiesStatesManager,
			$devicesPropertyStateRepository,
			$channelsPropertiesStatesManager,
			$channelPropertyStateRepository,
			$publisher,
			$entityManager
		);

		$entity = new Entities\Devices\PhysicalDevice('device-name', 'device-name');
		$entity->setName('Device custom name');

		$eventArgs = Mockery::mock(ORM\Event\LifecycleEventArgs::class);
		$eventArgs
			->shouldReceive('getObject')
			->withNoArgs()
			->andReturn($entity)
			->times(1);

		$subscriber->postPersist($eventArgs);
	}

	public function testPublishUpdatedEntity(): void
	{
		$publisher = Mockery::mock(NodeExchangePublishers\IRabbitMqPublisher::class);
		$publisher
			->shouldReceive('publish')
			->withArgs(function (string $key, array $data): bool {
				unset($data['id']);

				Assert::same('fb.bus.node.entity.updated.device', $key);
				Assert::equal([
					'identifier' => 'device-name',
					'type'       => 'physical',
					'parent'     => null,
					'device'     => 'device-name',
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

		$devicesPropertiesStatesManager = Mockery::mock(Models\States\Devices\PropertiesManager::class);
		$devicesPropertyStateRepository = Mockery::mock(Models\States\Devices\PropertyRepository::class);
		$channelsPropertiesStatesManager = Mockery::mock(Models\States\Channels\PropertiesManager::class);
		$channelPropertyStateRepository = Mockery::mock(Models\States\Channels\PropertyRepository::class);

		$subscriber = new Subscribers\EntitiesSubscriber(
			$devicesPropertiesStatesManager,
			$devicesPropertyStateRepository,
			$channelsPropertiesStatesManager,
			$channelPropertyStateRepository,
			$publisher,
			$entityManager
		);

		$entity = new Entities\Devices\PhysicalDevice('device-name', 'device-name');
		$entity->setName('Device custom name');

		$eventArgs = Mockery::mock(ORM\Event\LifecycleEventArgs::class);
		$eventArgs
			->shouldReceive('getObject')
			->andReturn($entity)
			->times(1);

		$subscriber->postUpdate($eventArgs);
	}

	public function testPublishDeletedEntity(): void
	{
		$publisher = Mockery::mock(NodeExchangePublishers\IRabbitMqPublisher::class);
		$publisher
			->shouldReceive('publish')
			->withArgs(function (string $key, array $data): bool {
				unset($data['id']);

				Assert::same('fb.bus.node.entity.deleted.device', $key);
				Assert::equal([
					'identifier' => 'device-name',
					'type'       => 'physical',
					'parent'     => null,
					'device'     => 'device-name',
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

		$entity = new Entities\Devices\PhysicalDevice('device-name', 'device-name');
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

		$devicesPropertiesStatesManager = Mockery::mock(Models\States\Devices\PropertiesManager::class);
		$devicesPropertyStateRepository = Mockery::mock(Models\States\Devices\PropertyRepository::class);
		$channelsPropertiesStatesManager = Mockery::mock(Models\States\Channels\PropertiesManager::class);
		$channelPropertyStateRepository = Mockery::mock(Models\States\Channels\PropertyRepository::class);

		$subscriber = new Subscribers\EntitiesSubscriber(
			$devicesPropertiesStatesManager,
			$devicesPropertyStateRepository,
			$channelsPropertiesStatesManager,
			$channelPropertyStateRepository,
			$publisher,
			$entityManager
		);

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
				->times(1)
				->getMock()
				->shouldReceive('isScheduledForDelete')
				->andReturn(false)
				->getMock();

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
