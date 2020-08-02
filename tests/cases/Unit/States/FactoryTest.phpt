<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\States;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use PHPOnCouch;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class FactoryTest extends BaseMockeryTestCase
{

	/**
	 * @param string $class
	 * @param mixed[] $data
	 * @param string $entityClass
	 *
	 * @dataProvider ./../../../fixtures/States/createStateValidDocumentData.php
	 */
	public function testCreateEntity(string $class, array $data, string $entityClass): void
	{
		$document = Mockery::mock(PHPOnCouch\CouchDocument::class);
		$document
			->shouldReceive('getKeys')
			->andReturn(array_keys($data));

		$document
			->shouldReceive('get')
			->andReturnUsing(function ($key) use ($data) {
				return $data[$key];
			});

		$entityMock = Mockery::mock($entityClass);

		$entity = States\StateFactory::create($class, $document, $entityMock);

		Assert::true($entity instanceof $class);

		if (method_exists($entity, 'toArray')) {
			$formatted = $entity->toArray();

		} else {
			$formatted = [];
		}

		foreach ($data as $key => $value) {
			Assert::same((string) $value, (string) $formatted[$key]);
		}
	}

	/**
	 * @param string $class
	 * @param mixed[] $data
	 *
	 * @dataProvider ./../../../fixtures/States/createStateInvalidDocumentData.php
	 *
	 * @throws FastyBird\DevicesNode\Exceptions\InvalidStateException
	 */
	public function testCreateEntityFail(string $class, array $data): void
	{
		$document = Mockery::mock(PHPOnCouch\CouchDocument::class);
		$document
			->shouldReceive('getKeys')
			->andReturn(array_keys($data));

		$document
			->shouldReceive('get')
			->andReturnUsing(function ($key) use ($data) {
				return $data[$key];
			});

		States\StateFactory::create($class, $document);
	}

}

$test_case = new FactoryTest();
$test_case->run();
