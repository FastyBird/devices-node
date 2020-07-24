<?php declare(strict_types = 1);

/**
 * RowRepository.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           24.03.20
 */

namespace FastyBird\DevicesNode\Models\Channels\Configuration;

use Doctrine\Common;
use Doctrine\Persistence;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Queries;
use IPub\DoctrineOrmQuery;
use Nette;
use Throwable;

/**
 * Channel configuration row repository
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class RowRepository implements IRowRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var Persistence\ObjectRepository<Entities\Channels\Configuration\Row>[] */
	private $repository = [];

	public function __construct(Common\Persistence\ManagerRegistry $managerRegistry)
	{
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneBy(
		Queries\FindChannelConfigurationQuery $queryObject,
		string $type = Entities\Channels\Configuration\Row::class
	): ?Entities\Channels\Configuration\IRow {
		/** @var Entities\Channels\Configuration\IRow|null $property */
		$property = $queryObject->fetchOne($this->getRepository($type));

		return $property;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Throwable
	 */
	public function getResultSet(
		Queries\FindChannelConfigurationQuery $queryObject,
		string $type = Entities\Channels\Configuration\Row::class
	): DoctrineOrmQuery\ResultSet {
		$result = $queryObject->fetch($this->getRepository($type));

		if (!$result instanceof DoctrineOrmQuery\ResultSet) {
			throw new Exceptions\InvalidStateException('Result set for given query could not be loaded.');
		}

		return $result;
	}

	/**
	 * @param string $type
	 *
	 * @return Persistence\ObjectRepository<Entities\Channels\Configuration\Row>
	 *
	 * @phpstan-template T of Entities\Channels\Configuration\Row
	 * @phpstan-param    class-string<T> $type
	 */
	private function getRepository(string $type): Persistence\ObjectRepository
	{
		if (!isset($this->repository[$type])) {
			$this->repository[$type] = $this->managerRegistry->getRepository($type);
		}

		return $this->repository[$type];
	}

}
