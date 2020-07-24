<?php declare(strict_types = 1);

/**
 * IRowsManager.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           01.11.18
 */

namespace FastyBird\DevicesNode\Models\Devices\Configuration;

use FastyBird\DevicesNode\Entities;
use Nette\Utils;

/**
 * Devices configuration entities manager interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRowsManager
{

	/**
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Devices\Configuration\IRow
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Devices\Configuration\IRow;

	/**
	 * @param Entities\Devices\Configuration\IRow $entity
	 * @param Utils\ArrayHash $values
	 *
	 * @return Entities\Devices\Configuration\IRow
	 */
	public function update(
		Entities\Devices\Configuration\IRow $entity,
		Utils\ArrayHash $values
	): Entities\Devices\Configuration\IRow;

	/**
	 * @param Entities\Devices\Configuration\IRow $entity
	 *
	 * @return bool
	 */
	public function delete(
		Entities\Devices\Configuration\IRow $entity
	): bool;

}
