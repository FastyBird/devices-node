<?php declare(strict_types = 1);

/**
 * IFirmwareManager.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           19.03.20
 */

namespace FastyBird\DevicesNode\Models\Devices\PhysicalDevice;

use FastyBird\DevicesNode\Entities;
use Nette\Utils;

/**
 * Device firmware entities manager interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IFirmwareManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Devices\PhysicalDevice\IFirmware
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Devices\PhysicalDevice\IFirmware;

	/**
	 * @param Entities\Devices\PhysicalDevice\IFirmware $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Devices\PhysicalDevice\IFirmware
	 */
	public function update(
		Entities\Devices\PhysicalDevice\IFirmware $entity,
		Utils\ArrayHash $values
	): Entities\Devices\PhysicalDevice\IFirmware;

}
