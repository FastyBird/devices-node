<?php declare(strict_types = 1);

/**
 * ChannelsManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           23.04.17
 */

namespace FastyBird\DevicesNode\Models\Channels;

use Closure;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Models;
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;

/**
 * Channel channels entities manager
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @method onBeforeCreate(Entities\Channels\IChannel $entity, Utils\ArrayHash $values)
 * @method onAfterCreate(Entities\Channels\IChannel $entity)
 * @method onBeforeUpdate(Entities\Channels\IChannel $entity, Utils\ArrayHash $values)
 * @method onAfterUpdate(Entities\Channels\IChannel $entity, Entities\Channels\IChannel $oldEntity)
 * @method onBeforeDelete(Entities\Channels\IChannel $entity)
 * @method onAfterDelete()
 */
final class ChannelsManager implements IChannelsManager
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
	): Entities\Channels\IChannel {
		// Get entity creator
		$creator = $this->entityCrud->getEntityCreator();

		// Service events
		$creator->beforeAction[] = function (Entities\Channels\IChannel $entity, Utils\ArrayHash $values): void {
			$this->onBeforeCreate($entity, $values);
		};

		$creator->afterAction[] = function (Entities\Channels\IChannel $entity): void {
			$this->onAfterCreate($entity);
		};

		/** @var Entities\Channels\IChannel $entity */
		$entity = $creator->create($values);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\Channels\IChannel $entity,
		Utils\ArrayHash $values
	): Entities\Channels\IChannel {
		// Get entity updater
		$updater = $this->entityCrud->getEntityUpdater();

		// Backup old entity
		$oldEntity = clone $entity;

		// Service events
		$updater->beforeAction[] = function (Entities\Channels\IChannel $entity, Utils\ArrayHash $values): void {
			$this->onBeforeUpdate($entity, $values);
		};

		$updater->afterAction[] = function (Entities\Channels\IChannel $entity) use ($oldEntity): void {
			$this->onAfterUpdate($entity, $oldEntity);
		};

		/** @var Entities\Channels\IChannel $entity */
		$entity = $updater->update($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		Entities\Channels\IChannel $entity
	): bool {
		// Get entity deleter
		$deleter = $this->entityCrud->getEntityDeleter();

		// Service events
		$deleter->beforeAction[] = function (Entities\Channels\IChannel $entity): void {
			$this->onBeforeDelete($entity);
		};

		$deleter->afterAction[] = function (): void {
			$this->onAfterDelete();
		};

		// Delete entity from database
		return $deleter->delete($entity);
	}

}
