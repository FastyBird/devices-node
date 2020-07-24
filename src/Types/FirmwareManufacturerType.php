<?php declare(strict_types = 1);

/**
 * FirmwareManufacturerType.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Types
 * @since          0.1.0
 *
 * @date           27.07.18
 */

namespace FastyBird\DevicesNode\Types;

use Consistence;

/**
 * Doctrine2 DB type for machine device manufacturer column
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Types
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class FirmwareManufacturerType extends Consistence\Enum\Enum
{

	/**
	 * Define data types
	 */
	public const MANUFACTURER_GENERIC = 'generic';
	public const MANUFACTURER_FASTYBIRD = 'fastybird';

	/**
	 * @return string
	 */
	public function __toString(): string
	{
		return (string) self::getValue();
	}

}
