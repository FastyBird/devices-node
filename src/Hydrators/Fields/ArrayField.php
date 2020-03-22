<?php declare(strict_types = 1);

/**
 * ArrayField.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           07.03.18
 */

namespace FastyBird\DevicesNode\Hydrators\Fields;

use IPub\JsonAPIDocument;

/**
 * Entity array field
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ArrayField extends Field
{

	/** @var bool */
	private $isNullable = true;

	public function __construct(
		bool $isNullable,
		string $mappedName,
		string $fieldName,
		bool $isRequired,
		bool $isWritable
	) {
		parent::__construct($mappedName, $fieldName, $isRequired, $isWritable);

		$this->isNullable = $isNullable;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return mixed[]|null
	 */
	public function getValue(JsonAPIDocument\Objects\IStandardObject $attributes): ?array
	{
		$value = $attributes->get($this->getMappedName());

		if ($value instanceof JsonAPIDocument\Objects\IStandardObject) {
			return $value->toArray();
		}

		return $value === null ? ($this->isNullable ? [] : null) : (array) $value;
	}

	/**
	 * @return bool
	 */
	public function isNullable(): bool
	{
		return $this->isNullable;
	}

}
