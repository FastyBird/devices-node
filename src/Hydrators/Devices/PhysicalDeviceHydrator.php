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

	/** @var string */
	protected $translationDomain = 'node.devices';

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Devices\PhysicalDevice::class;
	}

}
