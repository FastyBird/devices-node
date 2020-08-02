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
 * @date           02.03.20
 */

namespace FastyBird\DevicesNode\Models\States\Devices;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\States;
use Nette\Utils;

/**
 * Devices properties states manager interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPropertiesManager
{

	/**
	 * @param Entities\Devices\Properties\IProperty $property
	 *
	 * @return States\Devices\IProperty
	 */
	public function create(
		Entities\Devices\Properties\IProperty $property
	): States\Devices\IProperty;

	/**
	 * @param States\Devices\IProperty $state
	 * @param Entities\Devices\Properties\IProperty $property
	 *
	 * @return States\Devices\IProperty
	 */
	public function update(
		States\Devices\IProperty $state,
		Entities\Devices\Properties\IProperty $property
	): States\Devices\IProperty;

	/**
	 * @param States\Devices\IProperty $state
	 * @param Entities\Devices\Properties\IProperty $property
	 * @param Utils\ArrayHash $values
	 *
	 * @return States\Devices\IProperty
	 */
	public function updateState(
		States\Devices\IProperty $state,
		Entities\Devices\Properties\IProperty $property,
		Utils\ArrayHash $values
	): States\Devices\IProperty;

	/**
	 * @param States\Devices\IProperty $state
	 *
	 * @return bool
	 */
	public function delete(
		States\Devices\IProperty $state
	): bool;

}
