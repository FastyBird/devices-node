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
 * @date           26.10.18
 */

namespace FastyBird\DevicesNode\Entities\Channels\Properties;

use Consistence\Doctrine\Enum\EnumAnnotation as Enum;
use Doctrine\ORM\Mapping as ORM;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Types;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_channels_properties",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Communication channels properties"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="channel_property_unique", columns={"property_property", "channel_id"})
 *     },
 *     indexes={
 *       @ORM\Index(name="property_property_idx", columns={"property_property"}),
 *       @ORM\Index(name="property_settable_idx", columns={"property_settable"}),
 *       @ORM\Index(name="property_queryable_idx", columns={"property_queryable"})
 *     }
 * )
 */
class Property extends Entities\Entity implements IProperty
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
	 * @ORM\Column(type="string", name="property_property", length=40, nullable=false)
	 */
	private $property;

	/**
	 * @var string|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="property_name", length=100, nullable=true, options={"default": null})
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
	 * @var Entities\Channels\IChannel
	 *
	 * @ORM\ManyToOne(targetEntity="FastyBird\DevicesNode\Entities\Channels\Channel", inversedBy="properties")
	 * @ORM\JoinColumn(name="channel_id", referencedColumnName="channel_id", onDelete="CASCADE", nullable=false)
	 */
	private $channel;

	/**
	 * @param Entities\Channels\IChannel $channel
	 * @param string $property
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Channels\IChannel $channel,
		string $property,
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->property = $property;
		$this->channel = $channel;

		$channel->addProperty($this);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getChannel(): Entities\Channels\IChannel
	{
		return $this->channel;
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

		if ($this->datatype !== null && $format !== null) {
			if ($this->datatype->equalsValue(Types\DatatypeType::DATA_TYPE_INTEGER)) {
				[$min, $max] = explode(':', $format) + [null, null];

				if ($min !== null && $max !== null && intval($min) <= intval($max)) {
					return [intval($min), intval($max)];
				}

			} elseif ($this->datatype->equalsValue(Types\DatatypeType::DATA_TYPE_FLOAT)) {
				[$min, $max] = explode(':', $format) + [null, null];

				if ($min !== null && $max !== null && floatval($min) <= floatval($max)) {
					return [floatval($min), floatval($max)];
				}

			} elseif ($this->datatype->equalsValue(Types\DatatypeType::DATA_TYPE_ENUM)) {
				$format = array_filter(array_map('trim', explode(',', $format)), function ($item): bool {
					return $item !== '';
				});

				return $format;

			} elseif ($this->datatype->equalsValue(Types\DatatypeType::DATA_TYPE_COLOR)) {
				if ($format === self::COLOR_DATA_TYPE_FORMAT_HSV) {
					return self::COLOR_DATA_TYPE_FORMAT_HSV;

				} elseif ($format === self::COLOR_DATA_TYPE_FORMAT_RGB) {
					return self::COLOR_DATA_TYPE_FORMAT_RGB;
				}
			}
		}

		return null;
	}

}
