<?php declare(strict_types = 1);

/**
 * Control.php
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

namespace FastyBird\DevicesNode\Entities\Devices\Controls;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\DevicesNode\Entities;
use IPub\DoctrineCrud;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_devices_controls",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Devices controls"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="device_control_unique", columns={"control_name", "device_id"})
 *     }
 * )
 */
class Control implements IControl
{

	use Entities\TIdentifiedEntity;
	use DoctrineCrud\Entities\TEntity;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="control_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", name="control_name", length=100, nullable=false)
	 */
	private $name;

	/**
	 * @var Entities\Devices\IDevice
	 *
	 * @ORM\ManyToOne(targetEntity="FastyBird\DevicesNode\Entities\Devices\Device", inversedBy="controls")
	 * @ORM\JoinColumn(name="device_id", referencedColumnName="device_id", onDelete="CASCADE", nullable=false)
	 */
	private $device;

	/**
	 * @param string $name
	 * @param Entities\Devices\IDevice $device
	 *
	 * @throws Throwable
	 */
	public function __construct(string $name, Entities\Devices\IDevice $device)
	{
		$this->id = Uuid\Uuid::uuid4();

		$this->name = $name;
		$this->device = $device;

		$device->addControl($this);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDevice(): Entities\Devices\IDevice
	{
		return $this->device;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName(): string
	{
		return $this->name;
	}

}
