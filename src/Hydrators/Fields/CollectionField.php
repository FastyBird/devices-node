<?php declare(strict_types = 1);

/**
 * CollectionField.php
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

use FastyBird\DevicesNode\Exceptions;
use IPub\JsonAPIDocument;

/**
 * Entity entities collection field
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class CollectionField extends EntityField
{

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return void
	 */
	public function getValue(JsonAPIDocument\Objects\IStandardObject $attributes): void
	{
		throw new Exceptions\InvalidStateException(sprintf('Collection field \'%s\' could not be mapped as attribute.', $this->getMappedName()));
	}

}
