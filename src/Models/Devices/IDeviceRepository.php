<?php declare(strict_types = 1);

/**
 * IDeviceRepository.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           28.07.18
 */

namespace FastyBird\DevicesNode\Models\Devices;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Queries;
use IPub\DoctrineOrmQuery;

/**
 * Device repository interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IDeviceRepository
{

	/**
	 * @param Queries\FindDevicesQuery $queryObject
	 * @param string $type
	 *
	 * @return Entities\Devices\IDevice|null
	 *
	 * @phpstan-template T of Entities\Devices\Device
	 * @phpstan-param    Queries\FindDevicesQuery<T> $queryObject
	 * @phpstan-param    class-string<T> $type
	 */
	public function findOneBy(
		Queries\FindDevicesQuery $queryObject,
		string $type = Entities\Devices\Device::class
	): ?Entities\Devices\IDevice;

	/**
	 * @param Queries\FindDevicesQuery $queryObject
	 * @param string $type
	 *
	 * @return Entities\Devices\IDevice[]
	 *
	 * @phpstan-template T of Entities\Devices\Device
	 * @phpstan-param    Queries\FindDevicesQuery<T> $queryObject
	 * @phpstan-param    class-string<T> $type
	 */
	public function findAllBy(
		Queries\FindDevicesQuery $queryObject,
		string $type = Entities\Devices\Device::class
	): array;

	/**
	 * @param Queries\FindDevicesQuery $queryObject
	 * @param string $type
	 *
	 * @return DoctrineOrmQuery\ResultSet
	 *
	 * @phpstan-template T of Entities\Devices\Device
	 * @phpstan-param    Queries\FindDevicesQuery<T> $queryObject
	 * @phpstan-param    class-string<T> $type
	 * @phpstan-return   DoctrineOrmQuery\ResultSet<T>
	 */
	public function getResultSet(
		Queries\FindDevicesQuery $queryObject,
		string $type = Entities\Devices\Device::class
	): DoctrineOrmQuery\ResultSet;

}
