<?php declare(strict_types = 1);

/**
 * Row.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           01.11.18
 */

namespace FastyBird\DevicesNode\Entities\Devices\Configuration;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\DevicesNode\Entities;
use IPub\DoctrineCrud;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_devices_configuration",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Devices configurations rows"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="device_configuration_unique", columns={"configuration_name", "device_id"})
 *     }
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="configuration_type", type="string", length=20)
 * @ORM\DiscriminatorMap({
 *    "boolean" = "FastyBird\DevicesNode\Entities\Devices\Configuration\BooleanRow",
 *    "number"  = "FastyBird\DevicesNode\Entities\Devices\Configuration\NumberRow",
 *    "select"  = "FastyBird\DevicesNode\Entities\Devices\Configuration\SelectRow",
 *    "text"    = "FastyBird\DevicesNode\Entities\Devices\Configuration\TextRow"
 * })
 * @ORM\MappedSuperclass
 */
abstract class Row implements IRow
{

	use Entities\TIdentifiedEntity;
	use Entities\TEntityParams;
	use DoctrineCrud\Entities\TEntity;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="configuration_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @ORM\Column(type="string", name="configuration_name", length=40, nullable=false)
	 */
	protected $name;

	/**
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="configuration_title", nullable=true, options={"default": null})
	 */
	protected $title = null;

	/**
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="text", name="configuration_comment", nullable=true, options={"default": null})
	 */
	protected $comment = null;

	/**
	 * @var mixed|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="configuration_default", nullable=true, options={"default": null})
	 */
	protected $default = null;

	/**
	 * @var Entities\Devices\IDevice
	 *
	 * @ORM\ManyToOne(targetEntity="FastyBird\DevicesNode\Entities\Devices\Device", inversedBy="configuration")
	 * @ORM\JoinColumn(name="device_id", referencedColumnName="device_id", onDelete="CASCADE", nullable=false)
	 */
	protected $device;

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param string $name
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Devices\IDevice $device,
		string $name,
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->name = $name;

		$this->device = $device;

		$device->addConfiguration($this);
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

	/**
	 * {@inheritDoc}
	 */
	public function setTitle(?string $title): void
	{
		$this->title = $title;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getTitle(): ?string
	{
		return $this->title;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setComment(?string $comment): void
	{
		$this->comment = $comment;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getComment(): ?string
	{
		return $this->comment;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setDefault($default): void
	{
		$this->default = $default;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDefault()
	{
		return $this->default;
	}

}
