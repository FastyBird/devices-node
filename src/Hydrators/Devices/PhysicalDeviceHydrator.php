<?php declare(strict_types = 1);

/**
 * PhysicalDeviceHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 * @since          1.0.0
 *
 * @date           07.06.19
 */

namespace FastyBird\DevicesNode\Hydrators\Devices;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Schemas;
use IPub\JsonAPIDocument;
use Nette\Utils;
use Ramsey\Uuid;
use Throwable;

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

	/** @var JsonAPIDocument\Objects\IResourceObjectCollection<mixed>|null */
	protected $included;

	/** @var string */
	protected $translationDomain = 'node.devices';

	/**
	 * {@inheritDoc}
	 */
	public function hydrate(
		JsonAPIDocument\Objects\IResourceObject $resource,
		$entity = null
	): Utils\ArrayHash {
		throw new Exceptions\InvalidStateException('This method is overridden. Use method `hydrateDevice` instead.');
	}

	/**
	 * @param JsonAPIDocument\Objects\IResourceObject<mixed> $resource
	 * @param JsonAPIDocument\Objects\IResourceObjectCollection<mixed>|null $included
	 * @param Entities\Devices\IPhysicalDevice|null $entity
	 *
	 * @return Utils\ArrayHash
	 *
	 * @throws Throwable
	 */
	public function hydrateDevice(
		JsonAPIDocument\Objects\IResourceObject $resource,
		?JsonAPIDocument\Objects\IResourceObjectCollection $included,
		?Entities\Devices\IPhysicalDevice $entity = null
	): Utils\ArrayHash {
		$this->included = $included;

		return parent::hydrate($resource, $entity);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Devices\PhysicalDevice::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IRelationship<mixed> $relationship
	 *
	 * @return mixed[]|null
	 */
	protected function hydrateCredentialsRelationship(JsonAPIDocument\Objects\IRelationship $relationship): ?array
	{
		if (!$relationship->isHasOne()) {
			return null;
		}

		if ($this->included !== null) {
			foreach ($this->included->getAll() as $included) {
				if (
					$relationship->getIdentifier() !== null
					&& $included->getIdentifier()->getId() === $relationship->getIdentifier()->getId()
				) {
					$attributes = $included->getAttributes()->toArray();
					$attributes['entity'] = Entities\Devices\Credentials\Credentials::class;

					if (Uuid\Uuid::isValid($included->getIdentifier()->getId())) {
						$attributes['id'] = Uuid\Uuid::fromString($included->getIdentifier()->getId());
					}

					return $attributes;
				}
			}
		}

		return null;
	}

}
