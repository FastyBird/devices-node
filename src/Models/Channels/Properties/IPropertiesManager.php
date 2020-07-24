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

namespace FastyBird\DevicesNode\Models\Channels\Properties;

use FastyBird\DevicesNode\Entities;
use Nette\Utils;

/**
 * Channels properties entities manager interface
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
	 * @return Entities\Channels\Properties\IProperty
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Channels\Properties\IProperty;

	/**
	 * @param Entities\Channels\Properties\IProperty $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Channels\Properties\IProperty
	 */
	public function update(
		Entities\Channels\Properties\IProperty $entity,
		Utils\ArrayHash $values
	): Entities\Channels\Properties\IProperty;

	/**
	 * @param Entities\Channels\Properties\IProperty $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Channels\Properties\IProperty $entity
	): bool;

}
