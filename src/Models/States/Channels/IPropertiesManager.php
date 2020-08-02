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

namespace FastyBird\DevicesNode\Models\States\Channels;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\States;
use Nette\Utils;

/**
 * Channels properties manager interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IPropertiesManager
{

	/**
	 * @param Entities\Channels\Properties\IProperty $property
	 *
	 * @return States\Channels\IProperty
	 */
	public function create(
		Entities\Channels\Properties\IProperty $property
	): States\Channels\IProperty;

	/**
	 * @param States\Channels\IProperty $state
	 * @param Entities\Channels\Properties\IProperty $property
	 *
	 * @return States\Channels\IProperty
	 */
	public function update(
		States\Channels\IProperty $state,
		Entities\Channels\Properties\IProperty $property
	): States\Channels\IProperty;

	/**
	 * @param States\Channels\IProperty $state
	 * @param Entities\Channels\Properties\IProperty $property
	 * @param Utils\ArrayHash $values
	 *
	 * @return States\Channels\IProperty
	 */
	public function updateState(
		States\Channels\IProperty $state,
		Entities\Channels\Properties\IProperty $property,
		Utils\ArrayHash $values
	): States\Channels\IProperty;

	/**
	 * @param States\Channels\IProperty $state
	 *
	 * @return bool
	 */
	public function delete(
		States\Channels\IProperty $state
	): bool;

}
