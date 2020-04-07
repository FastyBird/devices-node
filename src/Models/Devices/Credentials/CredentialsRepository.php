<?php declare(strict_types = 1);

/**
 * CredentialsRepository.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.3
 *
 * @date           07.04.20
 */

namespace FastyBird\DevicesNode\Models\Devices\Credentials;

use Doctrine\Common;
use Doctrine\ORM;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Queries;
use Nette;

/**
 * Device property structure repository
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class CredentialsRepository implements ICredentialsRepository
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var ORM\EntityRepository<Entities\Devices\Credentials\Credentials>|null */
	private $repository = null;

	public function __construct(Common\Persistence\ManagerRegistry $managerRegistry)
	{
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOneBy(
		Queries\FindDeviceCredentialsQuery $queryObject
	): ?Entities\Devices\Credentials\ICredentials {
		/** @var Entities\Devices\Credentials\ICredentials|null $property */
		$property = $queryObject->fetchOne($this->getRepository());

		return $property;
	}

	/**
	 * @return ORM\EntityRepository<Entities\Devices\Credentials\Credentials>
	 */
	private function getRepository(): ORM\EntityRepository
	{
		if ($this->repository === null) {
			$this->repository = $this->managerRegistry->getRepository(Entities\Devices\Credentials\Credentials::class);
		}

		return $this->repository;
	}

}
