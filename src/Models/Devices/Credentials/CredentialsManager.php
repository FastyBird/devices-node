<?php declare(strict_types = 1);

/**
 * CredentialsManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           28.07.18
 */

namespace FastyBird\DevicesNode\Models\Devices\Credentials;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Models;
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;

/**
 * Device credentials entities manager
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class CredentialsManager implements ICredentialsManager
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
	): Entities\Devices\Credentials\ICredentials {
		// Get entity creator
		/** @var Entities\Devices\Credentials\ICredentials $entity */
		$entity = $this->entityCrud->getEntityCreator()->create($values);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\Devices\Credentials\ICredentials $entity,
		Utils\ArrayHash $values
	): Entities\Devices\Credentials\ICredentials {
		/** @var Entities\Devices\Credentials\ICredentials $entity */
		$entity = $this->entityCrud->getEntityUpdater()->update($values, $entity);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		Entities\Devices\Credentials\ICredentials $entity
	): bool {
		// Delete entity from database
		return $this->entityCrud->getEntityDeleter()->delete($entity);
	}

}
