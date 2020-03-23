<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\Controllers;
use FastyBird\NodeWebServer\Http;
use IPub\DoctrineOrmQuery;
use Mockery;
use Psr\Http\Message\ServerRequestInterface;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';
require_once __DIR__ . '/../DbTestCase.php';

final class DevicesV1ControllerTest extends DbTestCase
{

	public function setUp(): void
	{
		$this->registerDatabaseSchemaFile(__DIR__ . '/../../../fixtures/dummy.data.sql');

		parent::setUp();
	}

	public function testReadAll(): void
	{
		$request = Mockery::mock(ServerRequestInterface::class);
		$response = Mockery::mock(Http\Response::class);
		$response
			->shouldReceive('withEntity')
			->withArgs(function ($entity): bool {
				Assert::type(Http\ScalarEntity::class, $entity);

				if ($entity instanceof Http\ScalarEntity) {
					$data = $entity->getData();

					Assert::type(DoctrineOrmQuery\ResultSet::class, $data);

					if ($data instanceof DoctrineOrmQuery\ResultSet) {
						Assert::same(3, $data->getTotalCount());
					}
				}

				return true;
			})
			->times(1);

		$controller = $this->getContainer()->getByType(Controllers\DevicesV1Controller::class);

		$response = $controller->index($request, $response);
	}

}

$test_case = new DevicesV1ControllerTest();
$test_case->run();
