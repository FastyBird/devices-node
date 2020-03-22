<?php declare(strict_types = 1);

/**
 * FindDevicesQuery.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Queries
 * @since          0.1.0
 *
 * @date           30.07.18
 */

namespace FastyBird\DevicesNode\Queries;

use Closure;
use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use IPub\DoctrineOrmQuery;
use Ramsey\Uuid;

/**
 * Find devices entities query
 *
 * @package          FastyBird:DevicesNode!
 * @subpackage       Queries
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Devices\Device
 * @phpstan-extends  DoctrineOrmQuery\QueryObject<T>
 */
class FindDevicesQuery extends DoctrineOrmQuery\QueryObject
{

	/** @var Closure[] */
	protected $filter = [];

	/** @var Closure[] */
	protected $select = [];

	/**
	 * @param Uuid\UuidInterface $id
	 *
	 * @return void
	 */
	public function byId(Uuid\UuidInterface $id): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($id): void {
			$qb->andWhere('d.id = :id')->setParameter('id', $id, Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 *
	 * @return void
	 */
	public function forParent(Entities\Devices\IDevice $device): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($device): void {
			$qb->andWhere('d.parent = :parent')->setParameter('parent', $device->getId(), Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @return void
	 */
	public function withSettableChannelProperties(): void
	{
		$this->select[] = function (ORM\QueryBuilder $qb): void {
			$qb->join('d.channels', 'channels');
			$qb->join('channels.properties', 'chProperties');
		};

		$this->filter[] = function (ORM\QueryBuilder $qb): void {
			$qb->andWhere('chProperties.settable = :settable')->setParameter('settable', true);
		};
	}

	/**
	 * @return void
	 */
	public function withChannels(): void
	{
		$this->select[] = function (ORM\QueryBuilder $qb): void {
			$qb->join('d.channels', 'channels');
		};

		$this->filter[] = function (ORM\QueryBuilder $qb): void {
			$qb->andWhere('SIZE(channels.children) = 0');
		};
	}

	/**
	 * @param string $sortBy
	 * @param string $sortDir
	 *
	 * @return void
	 */
	public function sortBy(string $sortBy, string $sortDir = Common\Collections\Criteria::ASC): void
	{
		if (!in_array($sortDir, [Common\Collections\Criteria::ASC, Common\Collections\Criteria::DESC], true)) {
			throw new Exceptions\InvalidArgumentException('Provided sortDir value is not valid.');
		}

		$this->filter[] = function (ORM\QueryBuilder $qb) use ($sortBy, $sortDir): void {
			$qb->addOrderBy($sortBy, $sortDir);
		};
	}

	/**
	 * @param ORM\EntityRepository<Entities\Devices\Device> $repository
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
	 * @param ORM\EntityRepository<Entities\Devices\Device> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateCountQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $this->createBasicDql($repository)->select('COUNT(d.id)');

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository<Entities\Devices\Device> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function createBasicDql(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $repository->createQueryBuilder('d');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
