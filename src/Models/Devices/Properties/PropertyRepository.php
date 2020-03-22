<?php declare(strict_types = 1);

/**
 * PropertyRepository.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           21.11.18
 */

namespace FastyBird\DevicesNode\Models\Devices\Properties;

use Doctrine\Common;
use Doctrine\DBAL\Types;
use Doctrine\ORM;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Queries;
use IPub\DoctrineOrmQuery;
use Nette;
use Throwable;

/**
 * Device property structure repository
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class PropertyRepository implements IPropertyRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var ORM\EntityRepository<Entities\Devices\Properties\Property>|null */
	private $repository = null;

	public function __construct(Common\Persistence\ManagerRegistry $managerRegistry)
	{
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getOneByIdentifier(string $identifier): Entities\Devices\Properties\IProperty
	{
		try {
			/** @var Entities\Devices\Properties\IProperty|null $property */
			$property = $this->getRepository()->findOneBy(['id' => $identifier]);

			if ($property === null) {
				throw new Exceptions\ItemNotFoundException(sprintf('Channel property entity with identifier "%s" was not found.', $identifier));
			}

		} catch (Types\ConversionException $ex) {
			throw new Exceptions\ItemNotFoundException(sprintf('Provided channel property identifier "%s" is not valid UUID identifier.', $identifier));
		}

		return $property;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Throwable
	 */
	public function getResultSet(
		Queries\FindDevicePropertiesQuery $queryObject
	): DoctrineOrmQuery\ResultSet {
		$result = $queryObject->fetch($this->getRepository());

		if (!$result instanceof DoctrineOrmQuery\ResultSet) {
			throw new Exceptions\InvalidStateException('Result set for given query could not be loaded.');
		}

		return $result;
	}

	/**
	 * @return ORM\EntityRepository<Entities\Devices\Properties\Property>
	 */
	private function getRepository(): ORM\EntityRepository
	{
		if ($this->repository === null) {
			$this->repository = $this->managerRegistry->getRepository(Entities\Devices\Properties\Property::class);
		}

		return $this->repository;
	}

}
