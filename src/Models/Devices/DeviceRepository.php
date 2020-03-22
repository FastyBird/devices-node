<?php declare(strict_types = 1);

/**
 * DeviceRepository.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           28.07.18
 */

namespace FastyBird\DevicesNode\Models\Devices;

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Queries;
use IPub\DoctrineOrmQuery;
use Nette;
use Throwable;

/**
 * Device repository
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DeviceRepository implements IDeviceRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var ORM\EntityRepository<Entities\Devices\Device>[] */
	private $repository = [];

	public function __construct(Common\Persistence\ManagerRegistry $managerRegistry)
	{
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneBy(
		Queries\FindDevicesQuery $queryObject,
		string $type = Entities\Devices\Device::class
	): ?Entities\Devices\IDevice {
		/** @var Entities\Devices\IDevice|null $device */
		$device = $queryObject->fetchOne($this->getRepository($type));

		return $device;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Throwable
	 */
	public function findAllBy(
		Queries\FindDevicesQuery $queryObject,
		string $type = Entities\Devices\Device::class
	): array {
		$result = $queryObject->fetch($this->getRepository($type));

		return is_array($result) ? $result : $result->toArray();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws Throwable
	 */
	public function getResultSet(
		Queries\FindDevicesQuery $queryObject,
		string $type = Entities\Devices\Device::class
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
	 * @return ORM\EntityRepository<Entities\Devices\Device>
	 *
	 * @phpstan-template T of Entities\Devices\Device
	 * @phpstan-param    class-string<T> $type
	 */
	private function getRepository(string $type): ORM\EntityRepository
	{
		if (!isset($this->repository[$type])) {
			$this->repository[$type] = $this->managerRegistry->getRepository($type);
		}

		return $this->repository[$type];
	}

}
