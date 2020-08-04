<?php declare(strict_types = 1);

namespace Tests\Cases;

use Consistence;
use DateTimeImmutable;
use FastyBird\DateTimeFactory;
use FastyBird\DevicesNode\Connections;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\States;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use PHPOnCouch;
use Psr\Log;
use Ramsey\Uuid;
use stdClass;
use Tester\Assert;

require_once __DIR__ . '/../../../../../bootstrap.php';

/**
 * @testCase
 */
final class PropertiesManagerTest extends BaseMockeryTestCase
{

	/** @var string|null */
	private $id = null;

	/**
	 * @param mixed[] $data
	 * @param mixed[] $dbData
	 * @param mixed[] $expected
	 *
	 * @dataProvider ./../../../../../fixtures/Models/States/createChannelPropertyValue.php
	 */
	public function testCreateEntity(array $data, array $dbData, array $expected): void
	{
		$this->id = null;

		$now = new DateTimeImmutable();

		$dateFactory = Mockery::mock(DateTimeFactory\DateTimeFactory::class);
		$dateFactory
			->shouldReceive('getNow')
			->andReturn($now);

		$couchDbClient = Mockery::mock(PHPOnCouch\CouchClient::class);
		$couchDbClient
			->shouldReceive('storeDoc')
			->withArgs(function (stdClass $create) use ($dbData, $now): bool {
				$this->id = $create->_id;

				foreach ($dbData as $key => $value) {
					Assert::equal($value, $create->$key);
				}

				Assert::equal($now->format(DATE_ATOM), $create->created);
				Assert::null($create->updated);

				return true;
			})
			->getMock()
			->shouldReceive('asCouchDocuments')
			->getMock()
			->shouldReceive('getDoc')
			->andReturnUsing(function () use ($dbData): PHPOnCouch\CouchDocument {
				$dbData['id'] = $this->id;

				/** @var Mockery\MockInterface|PHPOnCouch\CouchDocument $document */
				$document = Mockery::mock(PHPOnCouch\CouchDocument::class);
				$document
					->shouldReceive('id')
					->andReturn($dbData['id'])
					->getMock()
					->shouldReceive('get')
					->andReturnUsing(function ($key) use ($dbData) {
						return $dbData[$key];
					})
					->getMock()
					->shouldReceive('getKeys')
					->andReturn(array_keys($dbData));

				return $document;
			});

		$couchDbConnection = Mockery::mock(Connections\ICouchDbConnection::class);
		$couchDbConnection
			->shouldReceive('getClient')
			->andReturn($couchDbClient);

		$logger = Mockery::mock(Log\LoggerInterface::class);

		$manager = new Models\States\Channels\PropertiesManager($couchDbConnection, $dateFactory, $logger);

		$entityMock = Mockery::mock(Entities\Channels\Properties\Property::class);
		$entityMock
			->shouldReceive('toArray')
			->andReturn($data)
			->getMock()
			->shouldReceive('getDatatype')
			->andReturn($data['datatype']);

		$state = $manager->create($entityMock);

		$expected['id'] = $this->id;

		Assert::type(States\Channels\Property::class, $state);
		Assert::equal($expected, $state->toArray());
	}

	/**
	 * @param mixed[] $data
	 * @param mixed[] $originalData
	 * @param mixed[] $expected
	 *
	 * @dataProvider ./../../../../../fixtures/Models/States/updateChannelPropertyValue.php
	 */
	public function testUpdateEntity(array $data, array $originalData, array $expected): void
	{
		$now = new DateTimeImmutable();

		$dateFactory = Mockery::mock(DateTimeFactory\DateTimeFactory::class);
		$dateFactory
			->shouldReceive('getNow')
			->andReturn($now);

		/** @var Mockery\MockInterface|PHPOnCouch\CouchDocument $document */
		$document = Mockery::mock(PHPOnCouch\CouchDocument::class);
		$document
			->shouldReceive('setAutocommit')
			->getMock()
			->shouldReceive('get')
			->andReturnUsing(function ($key) use (&$originalData) {
				return $originalData[$key];
			})
			->getMock()
			->shouldReceive('set')
			->withArgs(function ($key, $value) use ($data, $now, &$originalData): bool {
				if ($key === 'updated') {
					Assert::equal($now->format(DATE_ATOM), $value);

				} elseif ($data[$key] instanceof Consistence\Enum\Enum) {
					Assert::equal((string) $data[$key], $value);

				} else {
					Assert::equal($data[$key], $value);
				}

				$originalData[$key] = $value;

				return true;
			})
			->getMock()
			->shouldReceive('record')
			->getMock()
			->shouldReceive('getKeys')
			->andReturn(array_keys($originalData))
			->getMock()
			->shouldReceive('id')
			->andReturn($originalData['id']);

		$couchDbClient = Mockery::mock(PHPOnCouch\CouchClient::class);
		$couchDbClient
			->shouldReceive('asCouchDocuments');

		$couchDbConnection = Mockery::mock(Connections\ICouchDbConnection::class);
		$couchDbConnection
			->shouldReceive('getClient')
			->andReturn($couchDbClient);

		$logger = Mockery::mock(Log\LoggerInterface::class);

		$manager = new Models\States\Channels\PropertiesManager($couchDbConnection, $dateFactory, $logger);

		$entityMock = Mockery::mock(Entities\Channels\Properties\Property::class);
		$entityMock
			->shouldReceive('toArray')
			->andReturn($data)
			->getMock()
			->shouldReceive('getDatatype')
			->andReturn($data['datatype']);

		$original = new States\Channels\Property($originalData['id'], $document, $entityMock);

		$state = $manager->update($original, $entityMock);

		Assert::type(States\Channels\Property::class, $state);
		Assert::equal($expected, $state->toArray());
	}

	public function testDeleteEntity(): void
	{
		$originalData = [
			'id'       => Uuid\Uuid::uuid4()->toString(),
			'device'   => 'device_name',
			'channel'  => 'channel_name',
			'property' => 'property_name',
		];

		$now = new DateTimeImmutable();

		$dateFactory = Mockery::mock(DateTimeFactory\DateTimeFactory::class);
		$dateFactory
			->shouldReceive('getNow')
			->andReturn($now);

		/** @var Mockery\MockInterface|PHPOnCouch\CouchDocument $document */
		$document = Mockery::mock(PHPOnCouch\CouchDocument::class);

		$couchDbClient = Mockery::mock(PHPOnCouch\CouchClient::class);
		$couchDbClient
			->shouldReceive('asCouchDocuments')
			->times(1)
			->getMock()
			->shouldReceive('getDoc')
			->withArgs([$originalData['id']])
			->andReturn($document)
			->times(1)
			->getMock()
			->shouldReceive('deleteDoc')
			->withArgs([$document])
			->times(1);

		$couchDbConnection = Mockery::mock(Connections\ICouchDbConnection::class);
		$couchDbConnection
			->shouldReceive('getClient')
			->andReturn($couchDbClient);

		$logger = Mockery::mock(Log\LoggerInterface::class);

		$manager = new Models\States\Channels\PropertiesManager($couchDbConnection, $dateFactory, $logger);

		$entityMock = Mockery::mock(Entities\Channels\Properties\Property::class);

		$original = new States\Channels\Property($originalData['id'], $document, $entityMock);

		Assert::true($manager->delete($original));
	}

}

$test_case = new PropertiesManagerTest();
$test_case->run();
