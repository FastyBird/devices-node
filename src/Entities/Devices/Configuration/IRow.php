<?php declare(strict_types = 1);

/**
 * IRow.php
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

use FastyBird\DevicesNode\Entities;

/**
 * Device configuration row entity interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRow extends Entities\IRow
{

	/**
	 * @return Entities\Devices\IDevice
	 */
	public function getDevice(): Entities\Devices\IDevice;

}
