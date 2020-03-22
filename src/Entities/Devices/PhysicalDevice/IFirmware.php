<?php declare(strict_types = 1);

/**
 * IFirmware.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           28.07.18
 */

namespace FastyBird\DevicesNode\Entities\Devices\PhysicalDevice;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Types;
use IPub\DoctrineTimestampable;

/**
 * Firmware info entity interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IFirmware extends Entities\IEntity,
	DoctrineTimestampable\Entities\IEntityCreated, DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @return Entities\Devices\IPhysicalDevice
	 */
	public function getDevice(): Entities\Devices\IPhysicalDevice;

	/**
	 * @param string|null $name
	 *
	 * @return void
	 */
	public function setName(?string $name): void;

	/**
	 * @return string|null
	 */
	public function getName(): ?string;

	/**
	 * @param string|null $manufacturer
	 *
	 * @return void
	 */
	public function setManufacturer(?string $manufacturer): void;

	/**
	 * @return Types\FirmwareManufacturerType
	 */
	public function getManufacturer(): Types\FirmwareManufacturerType;

	/**
	 * @param string|null $version
	 *
	 * @return void
	 */
	public function setVersion(?string $version): void;

	/**
	 * @return string|null
	 */
	public function getVersion(): ?string;

}
