<?php declare(strict_types = 1);

/**
 * FirmwareManager.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           19.03.20
 */

namespace FastyBird\DevicesNode\Models\Devices\PhysicalDevice;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Models;
use IPub\DoctrineCrud\Crud;
use Nette;
use Nette\Utils;

/**
 * Device firmware entities manager
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class FirmwareManager implements IFirmwareManager
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
	): Entities\Devices\PhysicalDevice\IFirmware {
		// Get entity creator
		$creator = $this->entityCrud->getEntityCreator();

		/** @var Entities\Devices\PhysicalDevice\IFirmware $entity */
		$entity = $creator->create($values);

		return $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		Entities\Devices\PhysicalDevice\IFirmware $entity,
		Utils\ArrayHash $values
	): Entities\Devices\PhysicalDevice\IFirmware {
		// Get entity updater
		$updater = $this->entityCrud->getEntityUpdater();

		/** @var Entities\Devices\PhysicalDevice\IFirmware $entity */
		$entity = $updater->update($values, $entity);

		return $entity;
	}

}
