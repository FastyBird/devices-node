<?php declare(strict_types = 1);

/**
 * Entity.php
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

/**
 * Entity field
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class EntityField extends Field
{

	/** @var string */
	private $className;

	/** @var bool */
	private $nullable;

	/** @var bool */
	private $isRelationship;

	public function __construct(
		string $className,
		bool $nullable,
		string $mappedName,
		bool $isRelationship,
		string $fieldName,
		bool $isRequired,
		bool $isWritable
	) {
		parent::__construct($mappedName, $fieldName, $isRequired, $isWritable);

		$this->className = $className;
		$this->nullable = $nullable;
		$this->isRelationship = $isRelationship;
	}

	/**
	 * @return string
	 */
	public function getClassName(): string
	{
		return $this->className;
	}

	/**
	 * @return bool
	 */
	public function isNullable(): bool
	{
		return $this->nullable;
	}

	/**
	 * @return bool
	 */
	public function isRelationship(): bool
	{
		return $this->isRelationship;
	}

}
