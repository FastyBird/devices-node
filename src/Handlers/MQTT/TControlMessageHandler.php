<?php declare(strict_types = 1);

/**
 * TControlMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 * @since          0.1.0
 *
 * @date           25.03.20
 */

namespace FastyBird\DevicesNode\Handlers\MQTT;

use FastyBird\DevicesModule;
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\DevicesNode\Exceptions;
use Nette\Utils;

/**
 * Control message consumer
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
trait TControlMessageHandler
{

	/**
	 * @param Utils\ArrayHash $schema
	 * @param bool $isForDevice
	 *
	 * @return mixed[]
	 */
	public function handleControlConfigurationSchema(
		Utils\ArrayHash $schema,
		bool $isForDevice
	): array {
		$toUpdate = [];

		/** @var Utils\ArrayHash $row */
		foreach ($schema as $row) {
			if ($row->offsetExists('type') && $row->offsetExists('configuration')) {
				$toUpdateRow = [];

				$toUpdateRow['configuration'] = $row->offsetGet('configuration');

				if ($row->offsetExists('name')) {
					$toUpdateRow['name'] = $row->offsetGet('name');

				} else {
					throw new Exceptions\InvalidStateException('Field name have to be set');
				}

				if ($row->offsetExists('comment')) {
					$toUpdateRow['comment'] = $row->offsetGet('comment');

				} else {
					$toUpdateRow['comment'] = null;
				}

				$toUpdateRow['default'] = null;
				$toUpdateRow['value'] = null;

				switch ($row->offsetGet('type')) {
					case DevicesModule\Constants::DATA_TYPE_NUMBER:
						if ($isForDevice) {
							$toUpdateRow['entity'] = DevicesModuleEntities\Devices\Configuration\NumberRow::class;

						} else {
							$toUpdateRow['entity'] = DevicesModuleEntities\Channels\Configuration\NumberRow::class;
						}

						foreach (['min', 'max', 'step', 'default'] as $field) {
							if ($row->offsetExists($field) && $row->offsetGet($field) !== null) {
								$toUpdateRow[$field] = (float) $row->offsetGet($field);

							} else {
								$toUpdateRow[$field] = null;
							}
						}
						break;

					case DevicesModule\Constants::DATA_TYPE_TEXT:
						if ($isForDevice) {
							$toUpdateRow['entity'] = DevicesModuleEntities\Devices\Configuration\TextRow::class;

						} else {
							$toUpdateRow['entity'] = DevicesModuleEntities\Channels\Configuration\TextRow::class;
						}

						if ($row->offsetExists('default') && $row->offsetGet('default') !== null) {
							$toUpdateRow['default'] = (string) $row->offsetGet('default');
						}
						break;

					case DevicesModule\Constants::DATA_TYPE_BOOLEAN:
						if ($isForDevice) {
							$toUpdateRow['entity'] = DevicesModuleEntities\Devices\Configuration\BooleanRow::class;

						} else {
							$toUpdateRow['entity'] = DevicesModuleEntities\Channels\Configuration\BooleanRow::class;
						}

						if ($row->offsetExists('default') && $row->offsetGet('default') !== null) {
							$toUpdateRow['default'] = (bool) $row->offsetGet('default');
						}
						break;

					case DevicesModule\Constants::DATA_TYPE_SELECT:
						if ($isForDevice) {
							$toUpdateRow['entity'] = DevicesModuleEntities\Devices\Configuration\SelectRow::class;

						} else {
							$toUpdateRow['entity'] = DevicesModuleEntities\Channels\Configuration\SelectRow::class;
						}

						if (
							$row->offsetExists('values')
							&& $row->offsetGet('values') instanceof Utils\ArrayHash
						) {
							$toUpdateRow['values'] = $row->offsetGet('values');

						} else {
							$toUpdateRow['values'] = [];
						}

						if ($row->offsetExists('default') && $row->offsetGet('default') !== null) {
							$toUpdateRow['default'] = (string) $row->offsetGet('default');
						}
						break;
				}

				$toUpdate[] = $toUpdateRow;
			}
		}

		return $toUpdate;
	}

}
