<?php declare(strict_types = 1);

/**
 * IPropertyRepository.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           02.03.20
 */

namespace FastyBird\DevicesNode\Models\States\Channels;

use FastyBird\DevicesNode\States;
use Ramsey\Uuid;

/**
 * Channel property repository interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPropertyRepository
{

	/**
	 * @param Uuid\UuidInterface $id
	 *
	 * @return States\Channels\IProperty|null
	 */
	public function findOne(
		Uuid\UuidInterface $id
	): ?States\Channels\IProperty;

	/**
	 * @param Uuid\UuidInterface $id
	 *
	 * @return int|float|string|bool|null
	 */
	public function findValue(
		Uuid\UuidInterface $id
	);

	/**
	 * @param Uuid\UuidInterface $id
	 *
	 * @return int|float|string|bool|null
	 */
	public function findExpected(
		Uuid\UuidInterface $id
	);

}