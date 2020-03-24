<?php declare(strict_types = 1);

/**
 * IRowRepository.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           24.03.20
 */

namespace FastyBird\DevicesNode\Models\Devices\Configuration;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Queries;
use IPub\DoctrineOrmQuery;

/**
 * Device configuration row repository interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRowRepository
{

	/**
	 * @param Queries\FindDeviceConfigurationQuery $queryObject
	 * @param string $type
	 *
	 * @return Entities\Devices\Configuration\IRow|null
	 *
	 * @phpstan-template T of Entities\Devices\Configuration\Row
	 * @phpstan-param    Queries\FindDeviceConfigurationQuery<T> $queryObject
	 * @phpstan-param    class-string<T> $type
	 */
	public function findOneBy(
		Queries\FindDeviceConfigurationQuery $queryObject,
		string $type = Entities\Devices\Configuration\Row::class
	): ?Entities\Devices\Configuration\IRow;

	/**
	 * @param Queries\FindDeviceConfigurationQuery $queryObject
	 * @param string $type
	 *
	 * @return DoctrineOrmQuery\ResultSet
	 *
	 * @phpstan-template T of Entities\Devices\Configuration\Row
	 * @phpstan-param    Queries\FindDeviceConfigurationQuery<T> $queryObject
	 * @phpstan-param    class-string<T> $type
	 * @phpstan-return   DoctrineOrmQuery\ResultSet<T>
	 */
	public function getResultSet(
		Queries\FindDeviceConfigurationQuery $queryObject,
		string $type = Entities\Devices\Configuration\Row::class
	): DoctrineOrmQuery\ResultSet;

}
