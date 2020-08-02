<?php declare(strict_types = 1);

/**
 * Property.php
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

use DateTimeImmutable;
use DateTimeInterface;
use FastyBird\DevicesNode\Types;
use Nette;
use PHPOnCouch;

/**
 * Property state
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     States
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class Property extends State implements IProperty
{

	use Nette\SmartObject;

	/** @var string|null */
	private $value = null;

	/** @var string|null */
	private $expected = null;

	/** @var bool */
	private $pending = false;

	/** @var string|null */
	private $created = null;

	/** @var string|null */
	private $updated = null;

	public function __construct(
		string $id,
		PHPOnCouch\CouchDocument $document
	) {
		parent::__construct($id, $document);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setValue($value): void
	{
		$this->value = $value !== null ? (string) $value : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getValue()
	{
		return $this->normalizeValue($this->value);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setExpected($expected): void
	{
		$this->expected = $expected !== null ? (string) $expected : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getExpected()
	{
		return $this->normalizeValue($this->expected);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPending(bool $pending): void
	{
		$this->pending = $pending;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isPending(): bool
	{
		return $this->pending;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setCreated(?string $created): void
	{
		$this->created = $created;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCreated(): ?DateTimeInterface
	{
		return $this->created !== null ? new DateTimeImmutable($this->created) : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setUpdated(?string $updated): void
	{
		$this->updated = $updated;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUpdated(): ?DateTimeInterface
	{
		return $this->updated !== null ? new DateTimeImmutable($this->updated) : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return array_merge([
			'value'     => $this->getValue(),
			'expected'  => $this->getExpected(),
			'pending'   => $this->isPending(),
		], parent::toArray());
	}

	/**
	 * @param string|null $value
	 *
	 * @return int|float|string|bool|null
	 */
	protected function normalizeValue(
		?string $value
	) {
		if ($value === null) {
			return null;
		}

		if ($this->getDatatype() !== null) {
			if ($this->getDatatype()->equalsValue(Types\DatatypeType::DATA_TYPE_INTEGER)) {
				return intval($value);

			} elseif ($this->getDatatype()->equalsValue(Types\DatatypeType::DATA_TYPE_FLOAT)) {
				return floatval($value);

			} elseif ($this->getDatatype()->equalsValue(Types\DatatypeType::DATA_TYPE_STRING)) {
				return $value;

			} elseif ($this->getDatatype()->equalsValue(Types\DatatypeType::DATA_TYPE_BOOLEAN)) {
				return $value === 'true' || $value === '1';

			} elseif ($this->getDatatype()->equalsValue(Types\DatatypeType::DATA_TYPE_ENUM)) {
				if (is_array($this->getFormat()) && count($this->getFormat()) > 0) {
					if (in_array($value, $this->getFormat(), true)) {
						return $value;
					}

					return null;
				}

				return $value;
			}
		}

		return $value;
	}

}
