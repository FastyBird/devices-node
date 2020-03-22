<?php declare(strict_types = 1);

/**
 * ControlManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           09.06.19
 */

namespace FastyBird\DevicesNode\Models\Devices\Controls;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Models;
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;

/**
 * Devices controls entities manager
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ControlsManager implements IControlsManager
{

	use Nette\SmartObject;

	/** @var Crud\IEntityCrud */
	private $entityCrud;

	public function __construct(
		Crud\IEntityCrud $entityCrud
	) {
		// Entity CRUD for handling entities
		$this->entityCrud = $entityCrud;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Devices\Controls\IControl {
		// Get entity creator
		$creator = $this->entityCrud->getEntityCreator();

		/** @var Entities\Devices\Controls\IControl $entity */
		$entity = $creator->create($values);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\Devices\Controls\IControl $entity,
		Utils\ArrayHash $values
	): Entities\Devices\Controls\IControl {
		// Get entity updater
		$updater = $this->entityCrud->getEntityUpdater();

		/** @var Entities\Devices\Controls\IControl $entity */
		$entity = $updater->update($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		Entities\Devices\Controls\IControl $entity
	): bool {
		// Get entity deleter
		$deleter = $this->entityCrud->getEntityDeleter();

		// Delete entity from database
		return $deleter->delete($entity);
	}

}
