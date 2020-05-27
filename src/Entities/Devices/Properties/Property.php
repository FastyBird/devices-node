<?php declare(strict_types = 1);

/**
 * Property.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           02.11.18
 */

namespace FastyBird\DevicesNode\Entities\Devices\Properties;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Types;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_devices_properties",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Devices properties"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="device_property_unique", columns={"property_property", "device_id"})
 *     },
 *     indexes={
 *       @ORM\Index(name="property_property_idx", columns={"property_property"}),
 *       @ORM\Index(name="property_settable_idx", columns={"property_settable"}),
 *       @ORM\Index(name="property_queryable_idx", columns={"property_queryable"})
 *     }
 * )
 */
class Property extends NodeDatabaseEntities\Entity implements IProperty
{

	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="property_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="required")
	 * @ORM\Column(type="string", name="property_property", length=10, nullable=false)
	 */
	private $property;

	/**
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="property_name", length=20, nullable=true, options={"default": null})
	 */
	private $name = null;

	/**
	 * @var bool
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="boolean", name="property_settable", nullable=false, options={"default": false})
	 */
	private $settable = false;

	/**
	 * @var bool
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="boolean", name="property_queryable", nullable=false, options={"default": false})
	 */
	private $queryable = false;

	/**
	 * @var Types\DatatypeType|null
	 *
	 * @Enum(class=Types\DatatypeType::class)
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string_enum", name="property_datatype", nullable=true, options={"default": null})
	 */
	private $datatype = null;

	/**
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="property_unit", nullable=true, options={"default": null})
	 */
	private $unit = null;

	/**
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="property_format", nullable=true, options={"default": null})
	 */
	private $format = null;

	/**
	 * @var Entities\Devices\IDevice
	 *
	 * @IPubDoctrine\Crud(is="required")
	 * @ORM\ManyToOne(targetEntity="FastyBird\DevicesNode\Entities\Devices\Device", inversedBy="properties")
	 * @ORM\JoinColumn(name="device_id", referencedColumnName="device_id", onDelete="CASCADE", nullable=false)
	 */
	private $device;

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param string $property
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Devices\IDevice $device,
		string $property,
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->property = $property;

		$this->device = $device;

		$device->addProperty($this);
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
	public function getProperty(): string
	{
		return $this->property;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setName(?string $name): void
	{
		$this->name = $name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName(): ?string
	{
		return $this->name;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setSettable(bool $settable): void
	{
		$this->settable = $settable;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isSettable(): bool
	{
		return $this->settable;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setQueryable(bool $queryable): void
	{
		$this->queryable = $queryable;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isQueryable(): bool
	{
		return $this->queryable;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setDatatype(?string $datatype): void
	{
		if ($datatype !== null && !Types\DatatypeType::isValidValue($datatype)) {
			throw new Exceptions\InvalidArgumentException(sprintf('Provided device state "%s" is not valid', $datatype));
		}

		$this->datatype = $datatype !== null ? Types\DatatypeType::get($datatype) : null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDatatype(): ?Types\DatatypeType
	{
		return $this->datatype;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setUnit(?string $unit): void
	{
		$this->unit = $unit;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUnit(): ?string
	{
		return $this->unit;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setFormat(?string $format): void
	{
		$this->format = $format;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFormat()
	{
		$format = $this->format;

		if ($this->datatype !== null) {
			if ($this->datatype->equalsValue(Types\DatatypeType::DATA_TYPE_INTEGER)) {
				if ($format !== null) {
					[$min, $max] = explode(':', $format) + [null, null];

					if ($min !== null && $max !== null && intval($min) <= intval($max)) {
						return [intval($min), intval($max)];
					}
				}

			} elseif ($this->datatype->equalsValue(Types\DatatypeType::DATA_TYPE_FLOAT)) {
				if ($format !== null) {
					[$min, $max] = explode(':', $format) + [null, null];

					if ($min !== null && $max !== null && floatval($min) <= floatval($max)) {
						return [floatval($min), floatval($max)];
					}
				}

			} elseif ($this->datatype->equalsValue(Types\DatatypeType::DATA_TYPE_ENUM)) {
				if ($format !== null) {
					$format = array_filter(array_map('trim', explode(',', $format)), function ($item): bool {
						return $item !== '';
					});

					return $format;
				}
			}
		}

		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'id'        => $this->getPlainId(),
			'property'  => $this->getProperty(),
			'name'      => $this->getName(),
			'settable'  => $this->isSettable(),
			'queryable' => $this->isQueryable(),
			'datatype'  => $this->getDatatype() !== null ? $this->getDatatype()->getValue() : null,
			'unit'      => $this->getUnit(),
			'format'    => $this->getFormat(),
			'device'    => $this->getDevice()->getIdentifier(),
		];
	}

}
