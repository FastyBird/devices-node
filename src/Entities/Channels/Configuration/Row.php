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
 * @date           26.10.18
 */

namespace FastyBird\DevicesNode\Entities\Channels\Configuration;

use Doctrine\ORM\Mapping as ORM;
use FastyBird\DevicesNode\Entities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use IPub\DoctrineTimestampable;
use Ramsey\Uuid;
use Throwable;

/**
 * @ORM\Entity
 * @ORM\Table(
 *     name="fb_channels_configuration",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Communication channels configurations rows"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="channel_configuration_unique", columns={"configuration_name", "channel_id"})
 *     },
 *     indexes={
 *       @ORM\Index(name="configuration_name_idx", columns={"configuration_name"})
 *     }
 * )
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="configuration_type", type="string", length=20)
 * @ORM\DiscriminatorMap({
 *    "boolean" = "FastyBird\DevicesNode\Entities\Channels\Configuration\BooleanRow",
 *    "number"  = "FastyBird\DevicesNode\Entities\Channels\Configuration\NumberRow",
 *    "select"  = "FastyBird\DevicesNode\Entities\Channels\Configuration\SelectRow",
 *    "text"    = "FastyBird\DevicesNode\Entities\Channels\Configuration\TextRow"
 * })
 * @ORM\MappedSuperclass
 *
 * @property-read string $type
 */
abstract class Row extends NodeDatabaseEntities\Entity implements IRow
{

	use NodeDatabaseEntities\TEntityParams;
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
	 * @IPubDoctrine\Crud(is="required")
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
	 * @var mixed|null
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="configuration_value", nullable=true, options={"default": null})
	 */
	protected $value = null;

	/**
	 * @var Entities\Channels\IChannel
	 *
	 * @IPubDoctrine\Crud(is="required")
	 * @ORM\ManyToOne(targetEntity="FastyBird\DevicesNode\Entities\Channels\Channel", inversedBy="configuration")
	 * @ORM\JoinColumn(name="channel_id", referencedColumnName="channel_id", onDelete="CASCADE", nullable=false)
	 */
	protected $channel;

	/**
	 * @param Entities\Channels\IChannel $channel
	 * @param string $name
	 * @param Uuid\UuidInterface|null $id
	 *
	 * @throws Throwable
	 */
	public function __construct(
		Entities\Channels\IChannel $channel,
		string $name,
		?Uuid\UuidInterface $id = null
	) {
		$this->id = $id ?? Uuid\Uuid::uuid4();

		$this->name = $name;

		$this->channel = $channel;

		$channel->addConfiguration($this);
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
	public function setDefault(?string $default): void
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

	/**
	 * {@inheritDoc}
	 */
	public function setValue(?string $value): void
	{
		$this->value = $value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getValue()
	{
		return $this->value;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'id'      => $this->getPlainId(),
			'type'    => $this->getType(),
			'name'    => $this->getName(),
			'title'   => $this->getTitle(),
			'comment' => $this->getComment(),
			'default' => $this->getDefault(),
			'value'   => $this->getValue(),
			'device'  => $this->getChannel()->getDevice()->getIdentifier(),
			'parent'  => $this->getChannel()->getDevice()->getParent() !== null ? $this->getChannel()->getDevice()->getParent()->getIdentifier() : null,
			'channel' => $this->getChannel()->getChannel(),
		];
	}

}
