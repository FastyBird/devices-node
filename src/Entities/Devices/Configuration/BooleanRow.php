<?php declare(strict_types = 1);

/**
 * BooleanRow.php
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
use Nette\Utils;

/**
 * @ORM\Entity
 */
class BooleanRow extends Row implements IBooleanRow
{

	/** @var string */
	protected $type = DevicesNode\Constants::DATA_TYPE_BOOLEAN;

	/**
	 * {@inheritDoc}
	 */
	public function getValue(): ?bool
	{
		if ($this->value === null) {
			return null;
		}

		return $this->value === '1' || Utils\Strings::lower((string) $this->value) === 'true';
	}

}
