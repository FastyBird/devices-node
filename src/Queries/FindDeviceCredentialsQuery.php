<?php declare(strict_types = 1);

/**
 * FindDeviceCredentialsQuery.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Queries
 * @since          0.1.3
 *
 * @date           07.04.20
 */

namespace FastyBird\DevicesNode\Queries;

use Closure;
use Doctrine\ORM;
use FastyBird\DevicesNode\Entities;
use IPub\DoctrineOrmQuery;

/**
 * Find device credentials entities query
 *
 * @package          FastyBird:DevicesNode!
 * @subpackage       Queries
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Devices\Credentials\Credentials
 * @phpstan-extends  DoctrineOrmQuery\QueryObject<T>
 */
class FindDeviceCredentialsQuery extends DoctrineOrmQuery\QueryObject
{

	/** @var Closure[] */
	private $filter = [];

	/** @var Closure[] */
	private $select = [];

	/**
	 * @param string $username
	 *
	 * @return void
	 */
	public function byUsername(string $username): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($username): void {
			$qb->andWhere('c.username = :username')->setParameter('username', $username);
		};
	}

	/**
	 * @param string $password
	 *
	 * @return void
	 */
	public function byPassword(string $password): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($password): void {
			$qb->andWhere('c.password = :password')->setParameter('password', $password);
		};
	}

	/**
	 * @param ORM\EntityRepository<Entities\Devices\Credentials\Credentials> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $this->createBasicDql($repository);

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository<Entities\Devices\Credentials\Credentials> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateCountQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $this->createBasicDql($repository)->select('COUNT(c.id)');

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository<Entities\Devices\Credentials\Credentials> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	private function createBasicDql(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $repository->createQueryBuilder('c');
		$qb->addSelect('device');
		$qb->join('c.device', 'device');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
