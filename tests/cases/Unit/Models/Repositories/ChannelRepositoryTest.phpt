<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Queries;
use IPub\DoctrineOrmQuery;
use Tester\Assert;

require_once __DIR__ . '/../../../../bootstrap.php';
require_once __DIR__ . '/../../DbTestCase.php';

final class ChannelRepositoryTest extends DbTestCase
{

	public function testReadOne(): void
	{
		/** @var Models\Channels\ChannelRepository $repository */
		$repository = $this->getContainer()->getByType(Models\Channels\ChannelRepository::class);

		$findQuery = new Queries\FindChannelsQuery();
		$findQuery->byChannel('channel-one');

		$entity = $repository->findOneBy($findQuery);

		Assert::true(is_object($entity));
		Assert::type(Entities\Channels\Channel::class, $entity);
		Assert::same('channel-one', $entity->getChannel());
	}

	public function testReadResultSet(): void
	{
		/** @var Models\Channels\ChannelRepository $repository */
		$repository = $this->getContainer()->getByType(Models\Channels\ChannelRepository::class);

		$findQuery = new Queries\FindChannelsQuery();

		$resultSet = $repository->getResultSet($findQuery);

		Assert::type(DoctrineOrmQuery\ResultSet::class, $resultSet);
		Assert::same(2, $resultSet->getTotalCount());
	}

}

$test_case = new ChannelRepositoryTest();
$test_case->run();