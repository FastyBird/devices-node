<?php declare(strict_types = 1);

/**
 * Mixed.php
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
 * Entity mixed value field
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class MixedField extends Field
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
	 * @return mixed|null
	 */
	public function getValue(JsonAPIDocument\Objects\IStandardObject $attributes)
	{
		return $attributes->get($this->getMappedName());
	}

	/**
	 * @return bool
	 */
	public function isNullable(): bool
	{
		return $this->isNullable;
	}

}
