<?php declare(strict_types = 1);

/**
 * RowsManager.php
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

namespace FastyBird\DevicesNode\Models\Channels\Configuration;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Models;
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;

/**
 * Device channel configuration entities manager
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class RowsManager implements IRowsManager
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
	): Entities\Channels\Configuration\IRow {
		/** @var Entities\Channels\Configuration\IRow $entity */
		$entity = $this->entityCrud->getEntityCreator()->create($values);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\Channels\Configuration\IRow $entity,
		Utils\ArrayHash $values
	): Entities\Channels\Configuration\IRow {
		/** @var Entities\Channels\Configuration\IRow $entity */
		$entity = $this->entityCrud->getEntityUpdater()->update($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		Entities\Channels\Configuration\IRow $entity
	): bool {
		// Delete entity from database
		return $this->entityCrud->getEntityDeleter()->delete($entity);
	}

}
