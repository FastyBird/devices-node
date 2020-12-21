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
 * @date           03.03.20
 */

namespace FastyBird\DevicesNode\Models;

use FastyBird\CouchDbStoragePlugin\Models as CouchDbStoragePluginModels;
use FastyBird\CouchDbStoragePlugin\States as CouchDbStoragePluginStates;
use FastyBird\DateTimeFactory;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\States as DevicesModuleStates;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\States;
use Nette\Utils;
use Ramsey\Uuid;

/**
 * Base properties manager
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class PropertiesManager implements DevicesModuleModels\States\IPropertiesManager
{

	/** @var CouchDbStoragePluginModels\IStatesManager */
	private CouchDbStoragePluginModels\IStatesManager $statesManager;

	/** @var DateTimeFactory\DateTimeFactory */
	private DateTimeFactory\DateTimeFactory $dateFactory;

	public function __construct(
		CouchDbStoragePluginModels\IStatesManager $statesManager,
		DateTimeFactory\DateTimeFactory $dateFactory
	) {
		$this->statesManager = $statesManager;
		$this->dateFactory = $dateFactory;
	}

	/**
	 * {@inheritDoc}
	 */
	public function create(
		Uuid\UuidInterface $id,
		Utils\ArrayHash $values
	): DevicesModuleStates\IProperty {
		$values->offsetSet('created', $this->dateFactory->getNow());

		/** @var DevicesModuleStates\IProperty $property */
		$property = $this->statesManager->create($id, $values, States\Property::class);

		return $property;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		DevicesModuleStates\IProperty $state,
		Utils\ArrayHash $values
	): DevicesModuleStates\IProperty {
		if (!$state instanceof CouchDbStoragePluginStates\IState) {
			throw new Exceptions\InvalidArgumentException(sprintf('Provided state is not instance of %s', CouchDbStoragePluginStates\IState::class));
		}

		$values->offsetSet('updated', $this->dateFactory->getNow());

		/** @var DevicesModuleStates\IProperty $property */
		$property = $this->statesManager->update($state, $values);

		return $property;
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateState(
		DevicesModuleStates\IProperty $state,
		Utils\ArrayHash $values
	): DevicesModuleStates\IProperty {
		if (!$state instanceof CouchDbStoragePluginStates\IState) {
			throw new Exceptions\InvalidArgumentException(sprintf('Provided state is not instance of %s', CouchDbStoragePluginStates\IState::class));
		}

		$values->offsetSet('updated', $this->dateFactory->getNow());

		/** @var DevicesModuleStates\IProperty $property */
		$property = $this->statesManager->update($state, $values);

		return $property;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(DevicesModuleStates\IProperty $state): bool
	{
		if (!$state instanceof CouchDbStoragePluginStates\IState) {
			throw new Exceptions\InvalidArgumentException(sprintf('Provided state is not instance of %s', CouchDbStoragePluginStates\IState::class));
		}

		return $this->statesManager->delete($state);
	}

}
