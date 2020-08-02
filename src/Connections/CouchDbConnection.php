<?php declare(strict_types = 1);

/**
 * CouchDbConnection.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Connections
 * @since          0.1.0
 *
 * @date           03.03.20
 */

namespace FastyBird\DevicesNode\Connections;

use FastyBird\NodeExchange\Exceptions as NodeExchangeExceptions;
use Nette;
use PHPOnCouch;
use Psr\Log;
use Throwable;

/**
 * Couch DB connection configuration
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Connections
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class CouchDbConnection implements ICouchDbConnection
{

	use Nette\SmartObject;

	/** @var string */
	private $database;

	/** @var string */
	private $host;

	/** @var int */
	private $port;

	/** @var string|null */
	private $username;

	/** @var string|null */
	private $password;

	/** @var PHPOnCouch\CouchClient|null */
	private $client = null;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		Log\LoggerInterface $logger,
		string $database,
		string $host = '127.0.0.1',
		int $port = 5984,
		?string $username = null,
		?string $password = null
	) {
		$this->database = $database;

		$this->host = $host;
		$this->port = $port;
		$this->username = $username;
		$this->password = $password;

		$this->logger = $logger;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getHost(): string
	{
		return $this->host;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPort(): int
	{
		return $this->port;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUsername(): ?string
	{
		return $this->username;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPassword(): ?string
	{
		return $this->password;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDatabase(): string
	{
		return $this->database;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Throwable
	 */
	public function getClient(): PHPOnCouch\CouchClient
	{
		if ($this->client !== null) {
			return $this->client;
		}

		try {
			$this->client = new PHPOnCouch\CouchClient($this->buildDsn(), $this->database);

			if (!$this->client->databaseExists()) {
				$this->client->createDatabase();
			}

			return $this->client;

		} catch (Throwable $ex) {
			// Log error action reason
			$this->logger->error('[COUCH_DB] Could not connect do database', [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeExchangeExceptions\TerminateException('Connection could not be established', 0, $ex);
		}
	}

	/**
	 * @return string
	 */
	private function buildDsn(): string
	{
		$credentials = null;

		if ($this->username !== null) {
			$credentials .= $this->username . ':';
		}

		if ($this->password !== null) {
			$credentials .= ':' . $this->password;
		}

		if ($credentials !== null) {
			$credentials = str_replace('::', ':', $credentials) . '@';
		}

		return 'http://' . $credentials . $this->host . ':' . $this->port;
	}

}
