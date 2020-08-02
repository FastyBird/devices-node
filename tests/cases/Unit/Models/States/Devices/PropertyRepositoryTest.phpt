<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\Connections;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\States;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Types;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use PHPOnCouch;
use Psr\Log;
use Ramsey\Uuid;
use Tester\Assert;

require_once __DIR__ . '/../../../../../bootstrap.php';

/**
 * @testCase
 */
final class PropertyRepositoryTest extends BaseMockeryTestCase
{

	public function testFetchEntity(): void
	{
		$id = Uuid\Uuid::uuid4();

		$data = [
			'id'       => $id->toString(),
			'datatype' => null,
		];

		$couchDbClient = $this->mockCouchDbWithDocument($id, $data);

		$repository = $this->createRepository($couchDbClient, $data);

		$state = $repository->findOne($id);

		Assert::type(States\Devices\Property::class, $state);
	}

	/**
	 * @param Uuid\UuidInterface $id
	 * @param mixed[] $data
	 * @param mixed $expected
	 *
	 * @dataProvider ./../../../../../fixtures/Models/States/fetchDevicePropertyValue.php
	 */
	public function testFetchValue(
		Uuid\UuidInterface $id,
		array $data,
		$expected
	): void {
		$couchDbClient = $this->mockCouchDbWithDocument($id, $data);

		$repository = $this->createRepository($couchDbClient, $data);

		$state = $repository->findOne($id);

		Assert::type(States\Devices\Property::class, $state);
		Assert::equal($expected, $state->getValue());
		Assert::equal($expected, $state->getValue());
	}

	/**
	 * @param Uuid\UuidInterface $id
	 * @param mixed[] $data
	 * @param mixed $expected
	 *
	 * @dataProvider ./../../../../../fixtures/Models/States/fetchDevicePropertyExpected.php
	 */
	public function testFetchExpected(
		Uuid\UuidInterface $id,
		array $data,
		$expected
	): void {
		$couchDbClient = $this->mockCouchDbWithDocument($id, $data);

		$repository = $this->createRepository($couchDbClient, $data);

		$state = $repository->findOne($id);

		Assert::type(States\Devices\Property::class, $state);
		Assert::equal($expected, $state->getExpected());
		Assert::equal($expected, $state->getExpected());
	}

	/**
	 * @param Mockery\MockInterface|Connections\ICouchDbConnection $couchDbClient
	 * @param mixed[] $data
	 *
	 * @return Models\States\Devices\PropertyRepository
	 */
	private function createRepository(
		Mockery\MockInterface $couchDbClient,
		array $data
	): Models\States\Devices\PropertyRepository {
		$logger = Mockery::mock(Log\LoggerInterface::class);

		$propertyMock = Mockery::mock(Entities\Devices\Properties\Property::class);
		$propertyMock
			->shouldReceive('getDatatype')
			->andReturn($data['datatype'] !== null ? Types\DatatypeType::get($data['datatype']) : null)
			->getMock()
			->shouldReceive('getFormat')
			->andReturn(isset($data['format']) ? $data['format'] : null)
			->getMock();

		$propertyRepository = Mockery::mock(Models\Devices\Properties\PropertyRepository::class);
		$propertyRepository
			->shouldReceive('findOneBy')
			->andReturn($propertyMock);

		return new Models\States\Devices\PropertyRepository($propertyRepository, $couchDbClient, $logger);
	}

	/**
	 * @param Uuid\UuidInterface $id
	 * @param mixed[] $data
	 *
	 * @return Mockery\MockInterface|Connections\ICouchDbConnection
	 */
	private function mockCouchDbWithDocument(
		Uuid\UuidInterface $id,
		array $data
	): Mockery\MockInterface {
		$data['_id'] = $data['id'];

		$couchDbClient = Mockery::mock(PHPOnCouch\CouchClient::class);
		$couchDbClient
			->shouldReceive('asCouchDocuments')
			->getMock()
			->shouldReceive('find')
			->with([
				'id' => [
					'$eq' => $id->toString(),
				],
			])
			->andReturn([(object) $data])
			->times(1);

		$couchDbConnection = Mockery::mock(Connections\ICouchDbConnection::class);
		$couchDbConnection
			->shouldReceive('getClient')
			->andReturn($couchDbClient);

		return $couchDbConnection;
	}

}

$test_case = new PropertyRepositoryTest();
$test_case->run();
