<?php declare(strict_types = 1);

/**
 * IProperty.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           07.08.20
 */

namespace FastyBird\DevicesNode\Entities;

use FastyBird\DevicesNode\Types;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineTimestampable;

/**
 * Device or channel property entity interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IProperty extends NodeDatabaseEntities\IEntity,
	DoctrineTimestampable\Entities\IEntityCreated, DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @return string
	 */
	public function getProperty(): string;

	/**
	 * @param string|null $name
	 *
	 * @return void
	 */
	public function setName(?string $name): void;

	/**
	 * @return string|null
	 */
	public function getName(): ?string;

	/**
	 * @param bool $settable
	 *
	 * @return void
	 */
	public function setSettable(bool $settable): void;

	/**
	 * @return bool
	 */
	public function isSettable(): bool;

	/**
	 * @param bool $queryable
	 *
	 * @return void
	 */
	public function setQueryable(bool $queryable): void;

	/**
	 * @return bool
	 */
	public function isQueryable(): bool;

	/**
	 * @param string|null $dataType
	 *
	 * @return void
	 */
	public function setDatatype(?string $dataType): void;

	/**
	 * @return Types\DatatypeType|null
	 */
	public function getDatatype(): ?Types\DatatypeType;

	/**
	 * @param string|null $units
	 *
	 * @return void
	 */
	public function setUnit(?string $units): void;

	/**
	 * @return string|null
	 */
	public function getUnit(): ?string;

	/**
	 * @param string|null $format
	 *
	 * @return void
	 */
	public function setFormat(?string $format): void;

	/**
	 * @return string[]|string|int[]|float[]|null
	 */
	public function getFormat();

	/**
	 * @return mixed[]
	 */
	public function toArray(): array;

}
