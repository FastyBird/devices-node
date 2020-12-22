<?php declare(strict_types = 1);

/**
 * Property.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
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
use FastyBird\CouchDbStoragePlugin\States as CouchDbStoragePluginStates;
use FastyBird\DevicesModule\States as DevicesModuleStates;

/**
 * Property state
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     States
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class Property extends CouchDbStoragePluginStates\State implements IProperty, DevicesModuleStates\IProperty
{

	public const CREATE_FIELDS = [
		0          => 'id',
		'value'    => null,
		'expected' => null,
		'pending'  => false,
	];

	public const UPDATE_FIELDS = [
		'value',
		'expected',
		'pending',
	];

	/** @var string|null */
	private ?string $value = null;

	/** @var string|null */
	private ?string $expected = null;

	/** @var bool */
	private bool $pending = false;

	/** @var string|null */
	private ?string $created = null;

	/** @var string|null */
	private ?string $updated = null;

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
	public function setCreated(?string $created): void
	{
		$this->created = $created;
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
	public function setUpdated(?string $updated): void
	{
		$this->updated = $updated;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getValue(): ?string
	{
		return $this->value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setValue(?string $value): void
	{
		$this->value = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getExpected(): ?string
	{
		return $this->expected;
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
	public function toArray(): array
	{
		return array_merge([
			'value'    => $this->getValue(),
			'expected' => $this->getExpected(),
			'pending'  => $this->isPending(),
		], parent::toArray());
	}

}
