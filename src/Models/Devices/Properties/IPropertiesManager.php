<?php declare(strict_types = 1);

/**
 * IPropertiesManager.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           02.11.18
 */

namespace FastyBird\DevicesNode\Models\Devices\Properties;

use FastyBird\DevicesNode\Entities;
use Nette\Utils;

/**
 * Devices properties entities manager interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPropertiesManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Devices\Properties\IProperty
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Devices\Properties\IProperty;

	/**
	 * @param Entities\Devices\Properties\IProperty $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Devices\Properties\IProperty
	 */
	public function update(
		Entities\Devices\Properties\IProperty $entity,
		Utils\ArrayHash $values
	): Entities\Devices\Properties\IProperty;

	/**
	 * @param Entities\Devices\Properties\IProperty $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Devices\Properties\IProperty $entity
	): bool;

}
