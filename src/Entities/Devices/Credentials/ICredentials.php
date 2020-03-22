<?php declare(strict_types = 1);

/**
 * Credentials.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           28.07.18
 */

namespace FastyBird\DevicesNode\Entities\Devices\Credentials;

use FastyBird\DevicesNode\Entities;
use IPub\DoctrineTimestampable;

/**
 * Device credentials entity interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface ICredentials extends Entities\IIdentifiedEntity,
	DoctrineTimestampable\Entities\IEntityCreated, DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @param string $username
	 *
	 * @return void
	 */
	public function setUsername(string $username): void;

	/**
	 * @return string
	 */
	public function getUsername(): string;

	/**
	 * @param string $password
	 *
	 * @return void
	 */
	public function setPassword(string $password): void;

	/**
	 * @return string
	 */
	public function getPassword(): string;

	/**
	 * @return Entities\Devices\IPhysicalDevice
	 */
	public function getDevice(): Entities\Devices\IPhysicalDevice;

}
