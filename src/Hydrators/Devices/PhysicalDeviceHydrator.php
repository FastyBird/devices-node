<?php declare(strict_types = 1);

/**
 * PhysicalDeviceHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           07.06.19
 */

namespace FastyBird\DevicesNode\Hydrators\Devices;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Schemas;
use IPub\JsonAPIDocument;
use Ramsey\Uuid;

/**
 * Hardware device entity hydrator
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class PhysicalDeviceHydrator extends DeviceHydrator
{

	/** @var string[] */
	protected $attributes = [
		'identifier',
		'title',
		'comment',
		'enabled',
	];

	/** @var string[] */
	protected $relationships = [
		Schemas\Devices\PhysicalDeviceSchema::RELATIONSHIPS_CREDENTIALS,
	];

	/** @var string */
	protected $translationDomain = 'node.devices';

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Devices\PhysicalDevice::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IRelationship<mixed> $relationship
	 * @param JsonAPIDocument\Objects\IResourceObjectCollection<JsonAPIDocument\Objects\IResourceObject>|null $included
	 *
	 * @return mixed[]|null
	 */
	protected function hydrateCredentialsRelationship(
		JsonAPIDocument\Objects\IRelationship $relationship,
		?JsonAPIDocument\Objects\IResourceObjectCollection $included = null
	): ?array {
		if (!$relationship->isHasOne()) {
			return null;
		}

		if ($included !== null) {
			foreach ($included->getAll() as $item) {
				if (
					$relationship->getIdentifier() !== null
					&& $item->getIdentifier()->getId() === $relationship->getIdentifier()->getId()
				) {
					$attributes = $item->getAttributes()->toArray();
					$attributes['entity'] = Entities\Devices\Credentials\Credentials::class;

					if (Uuid\Uuid::isValid($item->getIdentifier()->getId())) {
						$attributes['id'] = Uuid\Uuid::fromString($item->getIdentifier()->getId());
					}

					return $attributes;
				}
			}
		}

		return null;
	}

}
