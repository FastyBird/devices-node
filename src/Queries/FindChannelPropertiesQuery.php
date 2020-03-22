<?php declare(strict_types = 1);

/**
 * FindChannelPropertiesQuery.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Queries
 * @since          0.1.0
 *
 * @date           25.11.18
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
 * Find channel properties entities query
 *
 * @package          FastyBird:DevicesNode!
 * @subpackage       Queries
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Channels\Properties\Property
 * @phpstan-extends  DoctrineOrmQuery\QueryObject<T>
 */
class FindChannelPropertiesQuery extends DoctrineOrmQuery\QueryObject
{

	/** @var Closure[] */
	private $filter = [];

	/** @var Closure[] */
	private $select = [];

	/**
	 * @param Uuid\UuidInterface $id
	 *
	 * @return void
	 */
	public function byId(Uuid\UuidInterface $id): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($id): void {
			$qb->andWhere('p.id = :id')->setParameter('id', $id, Uuid\Doctrine\UuidBinaryType::NAME);
		};
	}

	/**
	 * @param string $property
	 *
	 * @return void
	 */
	public function byProperty(string $property): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($property): void {
			$qb->andWhere('p.property = :property')->setParameter('property', $property);
		};
	}

	/**
	 * @param Entities\Channels\IChannel $channel
	 *
	 * @return void
	 */
	public function forChannel(Entities\Channels\IChannel $channel): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($channel): void {
			$qb->andWhere('channel.id = :channel')->setParameter('channel', $channel->getId(), Uuid\Doctrine\UuidBinaryType::NAME);
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
	 * @param ORM\EntityRepository<Entities\Channels\Properties\Property> $repository
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
	 * @param ORM\EntityRepository<Entities\Channels\Properties\Property> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function doCreateCountQuery(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $this->createBasicDql($repository)->select('COUNT(p.id)');

		foreach ($this->select as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

	/**
	 * @param ORM\EntityRepository<Entities\Channels\Properties\Property> $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	private function createBasicDql(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $repository->createQueryBuilder('p');
		$qb->addSelect('channel');
		$qb->join('p.channel', 'channel');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
