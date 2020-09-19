<?php declare(strict_types = 1);

/**
 * TPropertyMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 * @since          0.1.0
 *
 * @date           07.08.20
 */

namespace FastyBird\DevicesNode\Consumers;

use FastyBird\DevicesNode\States;
use Nette\Utils;

/**
 * Property message consumer
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
trait TPropertyMessageHandler
{

	/**
	 * @param Utils\ArrayHash $message
	 *
	 * @return mixed[]
	 */
	protected function handlePropertyConfiguration(
		Utils\ArrayHash $message
	): array {
		$toUpdate = [];

		if ($message->offsetExists('name')) {
			$subResult = $this->setPropertyName($message->offsetGet('name'));

			$toUpdate = array_merge($toUpdate, $subResult);
		}

		if ($message->offsetExists('settable')) {
			$subResult = $this->setPropertySettable((bool) $message->offsetGet('settable'));

			$toUpdate = array_merge($toUpdate, $subResult);
		}

		if ($message->offsetExists('queryable')) {
			$subResult = $this->setPropertyQueryable((bool) $message->offsetGet('queryable'));

			$toUpdate = array_merge($toUpdate, $subResult);
		}

		if ($message->offsetExists('datatype')) {
			$subResult = $this->setPropertyDatatype($message->offsetGet('datatype'));

			$toUpdate = array_merge($toUpdate, $subResult);
		}

		if ($message->offsetExists('format')) {
			$subResult = $this->setPropertyFormat($message->offsetGet('format'));

			$toUpdate = array_merge($toUpdate, $subResult);
		}

		if ($message->offsetExists('unit')) {
			$subResult = $this->setPropertyUnit($message->offsetGet('unit'));

			$toUpdate = array_merge($toUpdate, $subResult);
		}

		return $toUpdate;
	}

	/**
	 * @param States\IProperty $property
	 * @param Utils\ArrayHash $message
	 *
	 * @return mixed[]
	 */
	protected function handlePropertyState(
		States\IProperty $property,
		Utils\ArrayHash $message
	): array {
		if (
			$message->offsetExists('expected')
			|| $message->offsetExists('value')
		) {
			$message->offsetSet('pending', false);

			// In message is defined expected value
			if ($message->offsetExists('expected')) {
				$message->offsetSet('value', $property->getValue());

				// Expected value is same as value stored in device
				if ($property->getValue() === $property->normalizeValue($message->offsetGet('expected'))) {
					$message->offsetSet('pending', false);
					$message->offsetSet('expected', null);

				} else {
					$message->offsetSet('pending', true);
				}

			} elseif ($message->offsetExists('value')) {
				$message->offsetSet('expected', $property->getExpected());

				// Value is same as value expected value
				if ($property->getExpected() === $property->normalizeValue($message->offsetGet('value'))) {
					$message->offsetSet('pending', false);
					$message->offsetSet('expected', null);

				} elseif ($property->getExpected() !== null) {
					$message->offsetSet('pending', true);
				}

			} else {
				$message->offsetSet('expected', null);
			}

			return [
				'value'    => $message->offsetGet('value'),
				'expected' => $message->offsetGet('expected'),
				'pending'  => (bool) $message->offsetGet('pending'),
			];
		}

		return [];
	}

	/**
	 * @param string $name
	 *
	 * @return mixed[]
	 */
	protected function setPropertyName(
		string $name
	): array {
		return [
			'name' => $name,
		];
	}

	/**
	 * @param bool $settable
	 *
	 * @return mixed[]
	 */
	protected function setPropertySettable(
		bool $settable
	): array {
		return [
			'settable' => $settable,
		];
	}

	/**
	 * @param bool $queryable
	 *
	 * @return mixed[]
	 */
	protected function setPropertyQueryable(
		bool $queryable
	): array {
		return [
			'queryable' => $queryable,
		];
	}

	/**
	 * @param string|null $datatype
	 *
	 * @return mixed[]
	 */
	protected function setPropertyDatatype(
		?string $datatype
	): array {
		return [
			'datatype' => $datatype,
		];
	}

	/**
	 * @param string|null $format
	 *
	 * @return mixed[]
	 */
	protected function setPropertyFormat(
		?string $format
	): array {
		return [
			'format' => $format,
		];
	}

	/**
	 * @param string|null $unit
	 *
	 * @return mixed[]
	 */
	protected function setPropertyUnit(
		?string $unit
	): array {
		return [
			'unit' => $unit,
		];
	}

}
