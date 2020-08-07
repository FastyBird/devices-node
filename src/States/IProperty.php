<?php declare(strict_types = 1);

/**
 * IProperty.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     States
 * @since          0.1.0
 *
 * @date           03.03.20
 */

namespace FastyBird\DevicesNode\States;

use DateTimeInterface;
use FastyBird\DevicesNode\Types;

/**
 * Property interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     States
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IProperty extends IState
{

	/**
	 * @param float|int|string|null $value
	 *
	 * @return void
	 */
	public function setValue($value): void;

	/**
	 * @return float|int|bool|string|null
	 */
	public function getValue();

	/**
	 * @param float|int|string|null $expected
	 *
	 * @return void
	 */
	public function setExpected($expected): void;

	/**
	 * @return float|int|bool|string|null
	 */
	public function getExpected();

	/**
	 * @param bool $pending
	 *
	 * @return void
	 */
	public function setPending(bool $pending): void;

	/**
	 * @return bool
	 */
	public function isPending(): bool;

	/**
	 * @param string|null $created
	 */
	public function setCreated(?string $created): void;

	/**
	 * @return DateTimeInterface|null
	 */
	public function getCreated(): ?DateTimeInterface;

	/**
	 * @param string|null $created
	 */
	public function setUpdated(?string $created): void;

	/**
	 * @return DateTimeInterface|null
	 */
	public function getUpdated(): ?DateTimeInterface;

	/**
	 * @return Types\DatatypeType|null
	 */
	public function getDatatype(): ?Types\DatatypeType;

	/**
	 * @return string[]|string|int[]|float[]|null
	 */
	public function getFormat();

	/**
	 * @param string|null $value
	 *
	 * @return int|float|string|bool|null
	 */
	public function normalizeValue(?string $value);

}
