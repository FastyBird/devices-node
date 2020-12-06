<?php declare(strict_types = 1);

/**
 * TPropertyMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Handlers
 * @since          0.1.0
 *
 * @date           07.08.20
 */

namespace FastyBird\DevicesNode\Handlers\MQTT;

use FastyBird\MqttPlugin\Entities as MqttPluginEntities;

/**
 * Property message handler
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Handlers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
trait TPropertyMessageHandler
{

	/**
	 * @param MqttPluginEntities\Property $entity
	 *
	 * @return mixed[]
	 */
	protected function handlePropertyConfiguration(
		MqttPluginEntities\Property $entity
	): array {
		$toUpdate = [];

		foreach ($entity->getAttributes() as $attribute) {
			if ($attribute->getAttribute() === MqttPluginEntities\PropertyAttribute::NAME) {
				$subResult = $this->setPropertyName((string) $attribute->getValue());

				$toUpdate = array_merge($toUpdate, $subResult);
			}

			if ($attribute->getAttribute() === MqttPluginEntities\PropertyAttribute::SETTABLE) {
				$subResult = $this->setPropertySettable((bool) $attribute->getValue());

				$toUpdate = array_merge($toUpdate, $subResult);
			}

			if ($attribute->getAttribute() === MqttPluginEntities\PropertyAttribute::QUERYABLE) {
				$subResult = $this->setPropertyQueryable((bool) $entity->getValue());

				$toUpdate = array_merge($toUpdate, $subResult);
			}

			if ($attribute->getAttribute() === MqttPluginEntities\PropertyAttribute::DATATYPE) {
				$subResult = $this->setPropertyDatatype($entity->getValue());

				$toUpdate = array_merge($toUpdate, $subResult);
			}

			if ($attribute->getAttribute() === MqttPluginEntities\PropertyAttribute::FORMAT) {
				$subResult = $this->setPropertyFormat($entity->getValue());

				$toUpdate = array_merge($toUpdate, $subResult);
			}

			if ($attribute->getAttribute() === MqttPluginEntities\PropertyAttribute::SETTABLE) {
				$subResult = $this->setPropertyUnit($entity->getValue());

				$toUpdate = array_merge($toUpdate, $subResult);
			}
		}

		return $toUpdate;
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
