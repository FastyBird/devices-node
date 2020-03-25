<?php declare(strict_types = 1);

namespace Tests\Cases;

use Doctrine\DBAL;
use Doctrine\ORM;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\NodeLibs\Boot;
use Mockery;
use Nette\DI;
use Nettrine\DBAL as NettrineDBAL;
use Nettrine\ORM as NettrineORM;
use Ninjify\Nunjuck\TestCase\BaseMockeryTestCase;
use RuntimeException;
use Tests\Tools;

abstract class DbTestCase extends BaseMockeryTestCase
{

	/** @var DI\Container|null */
	private $container;

	/** @var bool */
	private $isDatabaseSetUp = false;

	/** @var string[] */
	private $sqlFiles = [];

	public function setUp(): void
	{
		$this->registerDatabaseSchemaFile(__DIR__ . '/../../sql/dummy.data.sql');

		parent::setUp();
	}

	/**
	 * @return DI\Container
	 */
	protected function getContainer(): DI\Container
	{
		if ($this->container === null) {
			$this->container = $this->createContainer();
		}

		return $this->container;
	}

	/**
	 * @param string $file
	 */
	protected function registerDatabaseSchemaFile(string $file): void
	{
		$this->sqlFiles[] = $file;
		$this->sqlFiles = array_unique($this->sqlFiles);
	}

	/**
	 * @param string $serviceType
	 * @param object $serviceMock
	 *
	 * @return void
	 */
	protected function mockContainerService(
		string $serviceType,
		object $serviceMock
	): void {
		$container = $this->getContainer();
		$foundServiceNames = $container->findByType($serviceType);

		foreach ($foundServiceNames as $serviceName) {
			$this->replaceContainerService($serviceName, $serviceMock);
		}
	}

	/**
	 * @return NettrineORM\EntityManagerDecorator
	 */
	protected function getEntityManager(): NettrineORM\EntityManagerDecorator
	{
		/** @var NettrineORM\EntityManagerDecorator $service */
		$service = $this->getContainer()->getByType(NettrineORM\EntityManagerDecorator::class);

		return $service;
	}

	/**
	 * @return DBAL\Connection
	 */
	protected function getDb(): DBAL\Connection
	{
		/** @var DBAL\Connection $service */
		$service = $this->getContainer()->getByType(DBAL\Connection::class);

		return $service;
	}

	/**
	 * @return void
	 */
	protected function tearDown(): void
	{
		$this->container = null; // Fatal error: Cannot redeclare class SystemContainer
		$this->isDatabaseSetUp = false;

		parent::tearDown();

		Mockery::close();
	}

	/**
	 * @return DI\Container
	 */
	private function createContainer(): DI\Container
	{
		$configurator = Boot\Bootstrap::boot();

		$this->container = $configurator->createContainer();

		/** @var NettrineDBAL\ConnectionFactory $connectionFactory */
		$connectionFactory = $this->container->getService('nettrineDbal.connectionFactory');

		$parameters = $this->container->getParameters();
		$parameters['database']['user'] = $parameters['database']['username'];
		$parameters['database']['wrapperClass'] = Tools\ConnectionWrapper::class;

		$connection = $connectionFactory->createConnection(
			$parameters['database'],
			$this->container->getService('nettrineDbal.configuration'),
			$this->container->getService('nettrineDbal.eventManager')
		);

		$this->container->removeService('nettrineDbal.connection');
		$this->container->addService('nettrineDbal.connection', $connection);

		$this->setupDatabase();

		return $this->container;
	}

	/**
	 * @param string $serviceName
	 * @param object $service
	 *
	 * @return void
	 */
	private function replaceContainerService(string $serviceName, object $service): void
	{
		$container = $this->getContainer();

		$container->removeService($serviceName);
		$container->addService($serviceName, $service);
	}

	/**
	 * @return void
	 */
	private function setupDatabase(): void
	{
		if (!$this->isDatabaseSetUp) {
			$db = $this->getDb();

			$metadatas = $this->getEntityManager()->getMetadataFactory()->getAllMetadata();
			$schemaTool = new ORM\Tools\SchemaTool($this->getEntityManager());

			$schemas = $schemaTool->getCreateSchemaSql($metadatas);

			foreach ($schemas as $sql) {
				try {
					$db->exec($sql);

				} catch (DBAL\DBALException $ex) {
					throw new RuntimeException('Database schema could not be created');
				}
			}

			foreach ($this->sqlFiles as $file) {
				$this->loadFromFile($db, $file);
			}

			$this->isDatabaseSetUp = true;
		}
	}

	/**
	 * @param DBAL\Connection $db
	 * @param string $file
	 *
	 * @return int
	 */
	private function loadFromFile(DBAL\Connection $db, string $file): int
	{
		@set_time_limit(0); // intentionally @

		$handle = @fopen($file, 'r'); // intentionally @

		if ($handle === false) {
			throw new Exceptions\InvalidArgumentException(sprintf('Cannot open file "%s".', $file));
		}

		$count = 0;
		$delimiter = ';';
		$sql = '';

		while (!feof($handle)) {
			$content = fgets($handle);

			if ($content !== false) {
				$s = rtrim($content);

				if (substr($s, 0, 10) === 'DELIMITER ') {
					$delimiter = substr($s, 10);

				} elseif (substr($s, -strlen($delimiter)) === $delimiter) {
					$sql .= substr($s, 0, -strlen($delimiter));

					try {
						$db->query($sql);
						$sql = '';
						$count++;

					} catch (DBAL\DBALException $ex) {
						// File could not be loaded
					}

				} else {
					$sql .= $s . "\n";
				}
			}
		}

		if (trim($sql) !== '') {
			try {
				$db->query($sql);
				$count++;

			} catch (DBAL\DBALException $ex) {
				// File could not be loaded
			}
		}

		fclose($handle);

		return $count;
	}

}
