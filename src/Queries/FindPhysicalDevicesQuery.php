<?php declare(strict_types = 1);

/**
 * FindPhysicalDevicesQuery.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Queries
 * @since          0.1.0
 *
 * @date           19.03.20
 */

namespace FastyBird\DevicesNode\Queries;

use Doctrine\ORM;
use FastyBird\DevicesNode\Entities;

/**
 * Find physical devices entities query
 *
 * @package          FastyBird:DevicesNode!
 * @subpackage       Queries
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Devices\PhysicalDevice
 * @phpstan-extends  FindDevicesQuery<T>
 */
class FindPhysicalDevicesQuery extends FindDevicesQuery
{

	/**
	 * @param string $identifier
	 *
	 * @return void
	 */
	public function byIdentifier(string $identifier): void
	{
		$this->filter[] = function (ORM\QueryBuilder $qb) use ($identifier): void {
			$qb->andWhere('pd.identifier = :identifier')->setParameter('identifier', $identifier);
		};
	}

	/**
	 * @param ORM\EntityRepository $repository
	 *
	 * @return ORM\QueryBuilder
	 *
	 * @phpstan-param ORM\EntityRepository<T> $repository
	 */
	protected function createBasicDql(ORM\EntityRepository $repository): ORM\QueryBuilder
	{
		$qb = $repository->createQueryBuilder('d');
		$qb->select('pd');
		$qb->leftJoin(Entities\Devices\PhysicalDevice::class, 'pd', ORM\Query\Expr\Join::WITH, 'd = pd');

		foreach ($this->filter as $modifier) {
			$modifier($qb);
		}

		return $qb;
	}

}
