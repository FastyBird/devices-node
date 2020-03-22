<?php declare(strict_types = 1);

/**
 * PropertiesManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           02.11.18
 */

namespace FastyBird\DevicesNode\Models\Devices\Properties;

use Closure;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Models;
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;

/**
 * Devices properties entities manager
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @method onBeforeCreate(Entities\Devices\Properties\IProperty $entity, Utils\ArrayHash $values)
 * @method onAfterCreate(Entities\Devices\Properties\IProperty $entity)
 * @method onBeforeUpdate(Entities\Devices\Properties\IProperty $entity, Utils\ArrayHash $values)
 * @method onAfterUpdate(Entities\Devices\Properties\IProperty $entity, Entities\Devices\Properties\IProperty $oldEntity)
 * @method onBeforeDelete(Entities\Devices\Properties\IProperty $entity)
 * @method onAfterDelete()
 */
final class PropertiesManager implements IPropertiesManager
{

	use Nette\SmartObject;

	/** @var Closure[] */
	public $onBeforeCreate = [];

	/** @var Closure[] */
	public $onAfterCreate = [];

	/** @var Closure[] */
	public $onBeforeUpdate = [];

	/** @var Closure[] */
	public $onAfterUpdate = [];

	/** @var Closure[] */
	public $onBeforeDelete = [];

	/** @var Closure[] */
	public $onAfterDelete = [];

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
	): Entities\Devices\Properties\IProperty {
		// Get entity creator
		$creator = $this->entityCrud->getEntityCreator();

		// Service events
		$creator->beforeAction[] = function (Entities\Devices\Properties\IProperty $entity, Utils\ArrayHash $values): void {
			$this->onBeforeCreate($entity, $values);
		};

		$creator->afterAction[] = function (Entities\Devices\Properties\IProperty $entity): void {
			$this->onAfterCreate($entity);
		};

		/** @var Entities\Devices\Properties\IProperty $entity */
		$entity = $creator->create($values);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\Devices\Properties\IProperty $entity,
		Utils\ArrayHash $values
	): Entities\Devices\Properties\IProperty {
		// Get entity updater
		$updater = $this->entityCrud->getEntityUpdater();

		// Backup old entity
		$oldEntity = clone $entity;

		// Service events
		$updater->beforeAction[] = function (Entities\Devices\Properties\IProperty $entity, Utils\ArrayHash $values): void {
			$this->onBeforeUpdate($entity, $values);
		};

		$updater->afterAction[] = function (Entities\Devices\Properties\IProperty $entity) use ($oldEntity): void {
			$this->onAfterUpdate($entity, $oldEntity);
		};

		/** @var Entities\Devices\Properties\IProperty $entity */
		$entity = $updater->update($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		Entities\Devices\Properties\IProperty $entity
	): bool {
		// Get entity deleter
		$deleter = $this->entityCrud->getEntityDeleter();

		// Service events
		$deleter->beforeAction[] = function (Entities\Devices\Properties\IProperty $entity): void {
			$this->onBeforeDelete($entity);
		};

		$deleter->afterAction[] = function (): void {
			$this->onAfterDelete();
		};

		// Delete entity from database
		return $deleter->delete($entity);
	}

}
