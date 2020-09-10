<?php declare(strict_types = 1);

/**
 * IRow.php
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

use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineTimestampable;

/**
 * Device or channel configuration row entity interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRow extends NodeDatabaseEntities\IEntity,
	NodeDatabaseEntities\IEntityParams,
	DoctrineTimestampable\Entities\IEntityCreated, DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @return string
	 */
	public function getConfiguration(): string;

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
	 * @param string|null $comment
	 *
	 * @return void
	 */
	public function setComment(?string $comment): void;

	/**
	 * @return string|null
	 */
	public function getComment(): ?string;

	/**
	 * @param string|null $default
	 *
	 * @return void
	 */
	public function setDefault(?string $default): void;

	/**
	 * @return mixed|null
	 */
	public function getDefault();

	/**
	 * @param string|null $value
	 *
	 * @return void
	 */
	public function setValue(?string $value): void;

	/**
	 * @return mixed|null
	 */
	public function getValue();

	/**
	 * @return string
	 */
	public function getType(): string;

	/**
	 * @return mixed[]
	 */
	public function toArray(): array;

}
