<?php declare(strict_types = 1);

/**
 * IRow.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           26.10.18
 */

namespace FastyBird\DevicesNode\Entities\Channels\Configuration;

use FastyBird\DevicesNode\Entities;
use IPub\DoctrineTimestampable;

/**
 * Channel configuration row entity interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRow extends Entities\IEntity,
	Entities\IEntityParams,
	DoctrineTimestampable\Entities\IEntityCreated, DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @return Entities\Channels\IChannel
	 */
	public function getChannel(): Entities\Channels\IChannel;

	/**
	 * @return string
	 */
	public function getName(): string;

	/**
	 * @param string|null $title
	 *
	 * @return void
	 */
	public function setTitle(?string $title): void;

	/**
	 * @return string|null
	 */
	public function getTitle(): ?string;

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
