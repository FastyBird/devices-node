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
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_physicals_devices",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Physicals devices"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="device_identifier_unique", columns={"device_identifier"})
 *     }
 * )
 */
class PhysicalDevice extends Device implements IPhysicalDevice
{

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="device_identifier", length=50, nullable=false)
	 */
	protected $identifier;

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
	 * @var Entities\Devices\Credentials\ICredentials
	 *
	 * @IPubDoctrine\Crud(is={"required", "writable"})
	 * @ORM\OneToOne(targetEntity="FastyBird\DevicesNode\Entities\Devices\Credentials\Credentials", mappedBy="device", cascade={"persist", "remove"})
	 */
	protected $credentials;

	/**
	 * @param string $identifier
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(string $identifier, ?Uuid\UuidInterface $id = null)
	{
		parent::__construct($id);

		$this->identifier = $identifier;
	}

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
	public function setIdentifier(string $identifier): void
	{
		$this->identifier = $identifier;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getIdentifier(): string
	{
		return $this->identifier;
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

	/**
	 * {@inheritDoc}
	 */
	public function setCredentials(Entities\Devices\Credentials\ICredentials $credentials): void
	{
		$this->credentials = $credentials;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getCredentials(): ?Entities\Devices\Credentials\ICredentials
	{
		return $this->credentials;
	}

}
