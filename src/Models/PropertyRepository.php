<?php declare(strict_types = 1);

/**
 * PropertyRepository.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           02.03.20
 */

namespace FastyBird\DevicesNode\Models;

use FastyBird\CouchDbStoragePlugin\Models as CouchDbStoragePluginModels;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\States as DevicesModuleStates;
use FastyBird\DevicesNode\States;
use Nette;
use Ramsey\Uuid;

/**
 * Property state repository
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class PropertyRepository implements DevicesModuleModels\States\IPropertyRepository
{

	use Nette\SmartObject;

	/** @var CouchDbStoragePluginModels\IStateRepository */
	private CouchDbStoragePluginModels\IStateRepository $stateRepository;

	public function __construct(
		CouchDbStoragePluginModels\IStateRepository $stateRepository
	) {
		$this->stateRepository = $stateRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOne(
		Uuid\UuidInterface $id
	): ?DevicesModuleStates\IProperty {
		/** @var DevicesModuleStates\IProperty $state */
		$state = $this->stateRepository->findOne($id, States\Property::class);

		return $state;
	}

}
