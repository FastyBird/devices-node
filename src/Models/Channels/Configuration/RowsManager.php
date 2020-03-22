<?php declare(strict_types = 1);

/**
 * RowsManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           01.11.18
 */

namespace FastyBird\DevicesNode\Models\Channels\Configuration;

use Closure;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Models;
use IPub\DoctrineCrud;
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;
use ReflectionClass;
use ReflectionException;

/**
 * Device channel configuration entities manager
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @method onBeforeCreate(Entities\Channels\Configuration\IRow $entity, Utils\ArrayHash $values)
 * @method onAfterCreate(Entities\Channels\Configuration\IRow $entity)
 * @method onBeforeUpdate(Entities\Channels\Configuration\IRow $entity, Utils\ArrayHash $values)
 * @method onAfterUpdate(Entities\Channels\Configuration\IRow $entity, Entities\Channels\Configuration\IRow $oldEntity)
 * @method onBeforeDelete(Entities\Channels\Configuration\IRow $entity)
 * @method onAfterDelete()
 */
final class RowsManager implements IRowsManager
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
	 *
	 * @throws ReflectionException
	 */
	public function create(
		Utils\ArrayHash $values
	): Entities\Channels\Configuration\IRow {
		// Get entity creator
		$creator = $this->entityCrud->getEntityCreator();

		// Type have to be set...
		if (!$values->offsetExists('entity') || !class_exists($values->offsetGet('entity'))) {
			throw new Exceptions\InvalidArgumentException('Type of device configuration have to be set and have to be valid class');
		}

		$entityClass = $values->entity;

		$rc = new ReflectionClass($entityClass);

		$constructor = $rc->getConstructor();

		if ($constructor !== null) {
			/** @var Entities\Channels\Configuration\IRow $entity */
			$entity = $rc->newInstanceArgs(DoctrineCrud\Helpers::autowireArguments($constructor, (array) $values));

		} else {
			throw new Exceptions\InvalidArgumentException('Device configuration row entity could not be initialized');
		}

		// Service events
		$creator->beforeAction[] = function (Entities\Channels\Configuration\IRow $entity, Utils\ArrayHash $values): void {
			$this->onBeforeCreate($entity, $values);
		};

		$creator->afterAction[] = function (Entities\Channels\Configuration\IRow $entity): void {
			$this->onAfterCreate($entity);
		};

		/** @var Entities\Channels\Configuration\IRow $entity */
		$entity = $creator->create($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\Channels\Configuration\IRow $entity,
		Utils\ArrayHash $values
	): Entities\Channels\Configuration\IRow {
		// Get entity updater
		$updater = $this->entityCrud->getEntityUpdater();

		// Backup old entity
		$oldEntity = clone $entity;

		// Service events
		$updater->beforeAction[] = function (Entities\Channels\Configuration\IRow $entity, Utils\ArrayHash $values): void {
			$this->onBeforeUpdate($entity, $values);
		};

		$updater->afterAction[] = function (Entities\Channels\Configuration\IRow $entity) use ($oldEntity): void {
			$this->onAfterUpdate($entity, $oldEntity);
		};

		/** @var Entities\Channels\Configuration\IRow $entity */
		$entity = $updater->update($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		Entities\Channels\Configuration\IRow $entity
	): bool {
		// Get entity deleter
		$deleter = $this->entityCrud->getEntityDeleter();

		// Service events
		$deleter->beforeAction[] = function (Entities\Channels\Configuration\IRow $entity): void {
			$this->onBeforeDelete($entity);
		};

		$deleter->afterAction[] = function (): void {
			$this->onAfterDelete();
		};

		// Delete entity from database
		return $deleter->delete($entity);
	}

}
