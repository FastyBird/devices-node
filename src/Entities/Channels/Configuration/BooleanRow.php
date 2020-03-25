<?php declare(strict_types = 1);

/**
 * BooleanRow.php
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

use Doctrine\ORM\Mapping as ORM;
use Nette\Utils;

/**
 * @ORM\Entity
 */
class BooleanRow extends Row implements IBooleanRow
{

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
