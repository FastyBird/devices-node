<?php declare(strict_types = 1);

/**
 * IHardware.php
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
 * Hardware info entity interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IHardware extends Entities\IEntity,
	DoctrineTimestampable\Entities\IEntityCreated, DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @return Entities\Devices\IPhysicalDevice
	 */
	public function getDevice(): Entities\Devices\IPhysicalDevice;

	/**
	 * @param string|null $manufacturer
	 *
	 * @return void
	 */
	public function setManufacturer(?string $manufacturer): void;

	/**
	 * @return Types\HardwareManufacturerType
	 */
	public function getManufacturer(): Types\HardwareManufacturerType;

	/**
	 * @param string|null $model
	 *
	 * @return void
	 */
	public function setModel(?string $model): void;

	/**
	 * @return Types\ModelType
	 */
	public function getModel(): Types\ModelType;

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

	/**
	 * @param string|null $macAddress
	 *
	 * @return void
	 */
	public function setMacAddress(?string $macAddress): void;

	/**
	 * @param string $separator
	 *
	 * @return string|null
	 */
	public function getMacAddress(string $separator = ':'): ?string;

}
