<?php declare(strict_types = 1);

/**
 * TPropertyMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 * @since          0.1.0
 *
 * @date           07.08.20
 */

namespace FastyBird\DevicesNode\Consumers\Bus;

use FastyBird\CouchDbStoragePlugin\States as CouchDbStoragePluginStates;
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\DevicesModule\Helpers as DevicesModuleHelpers;
use Nette\Utils;

/**
 * Property message consumer
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @property-read DevicesModuleHelpers\PropertyHelper $propertyHelper
 */
trait TPropertyMessageHandler
{

	/**
	 * @param DevicesModuleEntities\IProperty $property
	 * @param CouchDbStoragePluginStates\IProperty $state
	 * @param Utils\ArrayHash $message
	 *
	 * @return mixed[]
	 */
	protected function handlePropertyState(
		DevicesModuleEntities\IProperty $property,
		CouchDbStoragePluginStates\IProperty $state,
		Utils\ArrayHash $message
	): array {
		$message->offsetSet('pending', false);

		$message->offsetSet('value', $this->propertyHelper->normalizeValue($property, $state->getValue()));

		// Expected value is same as value stored in device
		if ($this->propertyHelper->normalizeValue($property, $state->getValue()) === $this->propertyHelper->normalizeValue($property, $message->offsetGet('expected'))) {
			$message->offsetSet('pending', false);
			$message->offsetSet('expected', null);

		} else {
			$message->offsetSet('pending', true);
		}

		return [
			'value'    => $this->propertyHelper->normalizeValue($property, $message->offsetGet('value')),
			'expected' => $this->propertyHelper->normalizeValue($property, $message->offsetGet('expected')),
			'pending'  => (bool) $message->offsetGet('pending'),
		];
	}

}
