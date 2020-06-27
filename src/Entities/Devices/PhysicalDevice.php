<?php declare(strict_types = 1);

/**
 * PhysicalDevice.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           11.05.19
 */

namespace FastyBird\DevicesNode\Entities\Devices;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\DevicesNode\Entities;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_physicals_devices",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Physicals devices"
 *     }
 * )
 */
class PhysicalDevice extends Device implements IPhysicalDevice
{

	/**
	 * @var Entities\Devices\PhysicalDevice\IHardware|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\OneToOne(targetEntity="FastyBird\DevicesNode\Entities\Devices\PhysicalDevice\Hardware", mappedBy="device", cascade={"persist", "remove"})
	 */
	protected $hardware;

	/**
	 * @var Entities\Devices\PhysicalDevice\IFirmware|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\OneToOne(targetEntity="FastyBird\DevicesNode\Entities\Devices\PhysicalDevice\Firmware", mappedBy="device", cascade={"persist", "remove"})
	 */
	protected $firmware;

	/**
	 * {@inheritDoc}
	 */
	public function getName(): string
	{
		return $this->name ?? $this->identifier;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setHardware(?Entities\Devices\PhysicalDevice\IHardware $hardware): void
	{
		$this->hardware = $hardware;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getHardware(): ?Entities\Devices\PhysicalDevice\IHardware
	{
		return $this->hardware;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setFirmware(?Entities\Devices\PhysicalDevice\IFirmware $firmware): void
	{
		$this->firmware = $firmware;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFirmware(): ?Entities\Devices\PhysicalDevice\IFirmware
	{
		return $this->firmware;
	}

}
