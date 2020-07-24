<?php declare(strict_types = 1);

/**
 * TextRow.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           01.11.18
 */

namespace FastyBird\DevicesNode\Entities\Devices\Configuration;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\DevicesNode;

/**
 * @ORM\Entity
 */
class TextRow extends Row implements ITextRow
{

	/** @var string */
	protected $type = DevicesNode\Constants::DATA_TYPE_TEXT;

	/**
	 * {@inheritDoc}
	 */
	public function getValue(): ?string
	{
		if ($this->value === null) {
			return null;
		}

		return (string) $this->value;
	}

}
