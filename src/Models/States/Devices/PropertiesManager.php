<?php declare(strict_types = 1);

/**
 * PropertiesManager.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           02.03.20
 */

namespace FastyBird\DevicesNode\Models\States\Devices;

use Closure;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\States;
use Nette\Utils;
use Throwable;

/**
 * Devices properties states manager
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @method onAfterCreate(States\Devices\IProperty $state)
 * @method onAfterUpdate(States\Devices\IProperty $state, States\Devices\IProperty $old)
 * @method onAfterDelete(States\Devices\IProperty $state)
 */
class PropertiesManager extends Models\States\PropertiesManager implements IPropertiesManager
{

	/** @var Closure[] */
	public $onAfterCreate = [];

	/** @var Closure[] */
	public $onAfterUpdate = [];

	/** @var Closure[] */
	public $onAfterDelete = [];

	/**
	 * {@inheritDoc}
	 */
	public function create(
		Entities\Devices\Properties\IProperty $property
	): States\Devices\IProperty {
		try {
			$doc = $this->createDoc(Utils\ArrayHash::from($property->toArray()), $this->getCreateFields());

			/** @var States\Devices\IProperty $state */
			$state = States\StateFactory::create(States\Devices\Property::class, $doc, $property);

		} catch (Throwable $ex) {
			$this->logger->error('[MODEL] Document could not be created', [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
				'data'      => [
					'property' => $property->getId()->toString(),
				],
			]);

			throw new Exceptions\InvalidStateException('Property could not be created', $ex->getCode(), $ex);
		}

		$this->onAfterCreate($state);

		return $state;
	}

	/**
	 * {@inheritDoc}
	 */
	public function update(
		States\Devices\IProperty $state,
		Entities\Devices\Properties\IProperty $property
	): States\Devices\IProperty {
		try {
			$doc = $this->updateDoc($state, Utils\ArrayHash::from($property->toArray()), $this->getUpdateFields());

			/** @var States\Devices\IProperty $updatedState */
			$updatedState = States\StateFactory::create(States\Devices\Property::class, $doc, $property);

		} catch (Exceptions\NotUpdatedException $ex) {
			return $state;

		} catch (Throwable $ex) {
			$this->logger->error('[MODEL] Document could not be updated', [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
				'data'      => [
					'property' => $state->getId()->toString(),
				],
			]);

			throw new Exceptions\InvalidStateException('Property could not be updated', $ex->getCode(), $ex);
		}

		$this->onAfterUpdate($updatedState, $state);

		return $updatedState;
	}

	/**
	 * {@inheritDoc}
	 */
	public function updateState(
		States\Devices\IProperty $state,
		Entities\Devices\Properties\IProperty $property,
		Utils\ArrayHash $values
	): States\Devices\IProperty {
		try {
			$doc = $this->updateDoc(
				$state,
				$values,
				[
					'value',
					'expected',
					'pending',
				]
			);

			/** @var States\Devices\IProperty $updatedState */
			$updatedState = States\StateFactory::create(States\Devices\Property::class, $doc, $property);

		} catch (Exceptions\NotUpdatedException $ex) {
			return $state;

		} catch (Throwable $ex) {
			$this->logger->error('[MODEL] Document could not be updated', [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
				'data'      => [
					'property' => $state->getId()->toString(),
				],
			]);

			throw new Exceptions\InvalidStateException('Property could not be updated', $ex->getCode(), $ex);
		}

		$this->onAfterUpdate($updatedState, $state);

		return $state;
	}

	/**
	 * {@inheritDoc}
	 */
	public function delete(
		States\Devices\IProperty $state
	): bool {
		$result = $this->deleteDoc($state->getId()->toString());

		if ($result === false) {
			return false;
		}

		$this->onAfterDelete($state);

		return true;
	}

	/**
	 * @return mixed[]
	 */
	protected function getCreateFields(): array
	{
		return [
			0           => 'id',
			'value'     => null,
			'expected'  => null,
			'pending'   => false,
		];
	}

	/**
	 * @return string[]
	 */
	protected function getUpdateFields(): array
	{
		return [
			'value',
			'expected',
			'pending',
		];
	}

}
