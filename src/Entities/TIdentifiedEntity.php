<?php declare(strict_types = 1);

/**
 * TIdentifiedEntity.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           03.02.18
 */

namespace FastyBird\DevicesNode\Entities;

use Ramsey\Uuid;

/**
 * Doctrine identified entity helper trait
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @property-read Uuid\UuidInterface $id
 */
trait TIdentifiedEntity
{

	/**
	 * @return Uuid\UuidInterface
	 */
	public function getId(): Uuid\UuidInterface
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getPlainId(): string
	{
		return $this->id->toString();
	}

}
