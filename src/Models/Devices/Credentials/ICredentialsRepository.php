<?php declare(strict_types = 1);

/**
 * ICredentialsRepository.php
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

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Queries;

/**
 * Device credentials repository interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface ICredentialsRepository
{

	/**
	 * @param Queries\FindDeviceCredentialsQuery $queryObject
	 *
	 * @return Entities\Devices\Credentials\ICredentials|null
	 *
	 * @phpstan-template T of Entities\Devices\Credentials\Credentials
	 * @phpstan-param    Queries\FindDeviceCredentialsQuery<T> $queryObject
	 */
	public function findOneBy(
		Queries\FindDeviceCredentialsQuery $queryObject
	): ?Entities\Devices\Credentials\ICredentials;

}
