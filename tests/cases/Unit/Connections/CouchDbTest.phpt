<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\Connections;
use FastyBird\NodeBootstrap\Boot;
use Mockery;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use Psr\Log;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class CouchDbTest extends BaseMockeryTestCase
{

	public function testDefaultValues(): void
	{
		$log = Mockery::mock(Log\LoggerInterface::class);

		$config = new Connections\CouchDbConnection($log, 'db.name');

		Assert::same('127.0.0.1', $config->getHost());
		Assert::same(5984, $config->getPort());
		Assert::null($config->getUsername());
		Assert::null($config->getPassword());
		Assert::same('db.name', $config->getDatabase());
	}

	public function testConfiguredViaEnv(): void
	{
		putenv('FB_NODE_PARAMETER__STORAGE_HOST=dbhost.loc');
		putenv('FB_NODE_PARAMETER__STORAGE_PORT=1234');

		$container = Boot\Bootstrap::boot()->createContainer();

		/** @var Connections\CouchDbConnection $connection */
		$connection = $container->getService('devicesPropertiesDatabaseConnection');

		Assert::same('dbhost.loc', $connection->getHost());
		Assert::same(1234, $connection->getPort());

		/** @var Connections\CouchDbConnection $connection */
		$connection = $container->getService('channelsPropertiesDatabaseConnection');

		Assert::same('dbhost.loc', $connection->getHost());
		Assert::same(1234, $connection->getPort());
	}

}

$test_case = new CouchDbTest();
$test_case->run();
