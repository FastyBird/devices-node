<?php declare(strict_types = 1);

/**
 * IPropertyRepository.php
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

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Queries;
use IPub\DoctrineOrmQuery;

/**
 * Device property repository interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPropertyRepository
{

	/**
	 * @param Queries\FindDevicePropertiesQuery $queryObject
	 *
	 * @return Entities\Devices\Properties\IProperty|null
	 *
	 * @phpstan-template T of Entities\Devices\Properties\Property
	 * @phpstan-param    Queries\FindDevicePropertiesQuery<T> $queryObject
	 */
	public function findOneBy(
		Queries\FindDevicePropertiesQuery $queryObject
	): ?Entities\Devices\Properties\IProperty;

	/**
	 * @param Queries\FindDevicePropertiesQuery $queryObject
	 *
	 * @return DoctrineOrmQuery\ResultSet
	 *
	 * @phpstan-template T of Entities\Devices\Properties\Property
	 * @phpstan-param    Queries\FindDevicePropertiesQuery<T> $queryObject
	 * @phpstan-return   DoctrineOrmQuery\ResultSet<T>
	 */
	public function getResultSet(
		Queries\FindDevicePropertiesQuery $queryObject
	): DoctrineOrmQuery\ResultSet;

}
