<?php declare(strict_types = 1);

/**
 * Credentials.php
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

namespace FastyBird\DevicesNode\Entities\Devices\Credentials;

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
 *     name="fb_physicals_devices_credentials",
 *     options={
 *       "collate"="utf8mb4_general_ci",
 *       "charset"="utf8mb4",
 *       "comment"="Physicals devices access credentials"
 *     },
 *     uniqueConstraints={
 *       @ORM\UniqueConstraint(name="credentials_username_unique", columns={"credentials_username"})
 *     }
 * )
 */
class Credentials implements ICredentials
{

	use Entities\TIdentifiedEntity;
	use DoctrineCrud\Entities\TEntity;
	use DoctrineTimestampable\Entities\TEntityCreated;
	use DoctrineTimestampable\Entities\TEntityUpdated;

	/**
	 * @var Uuid\UuidInterface
	 *
	 * @ORM\Id
	 * @ORM\Column(type="uuid_binary", name="credentials_id")
	 * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
	 */
	protected $id;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="credentials_username", length=40, nullable=false)
	 */
	private $username;

	/**
	 * @var string
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="string", name="credentials_password", length=40, nullable=false)
	 */
	private $password;

	/**
	 * @var Entities\Devices\IPhysicalDevice
	 *
	 * @ORM\OneToOne(targetEntity="FastyBird\DevicesNode\Entities\Devices\PhysicalDevice", inversedBy="credentials")
	 * @ORM\JoinColumn(name="device_id", referencedColumnName="device_id", unique=true, onDelete="CASCADE", nullable=false)
	 */
	private $device;

	/**
	 * @param string $username
	 * @param string $password
	 * @param Entities\Devices\IPhysicalDevice $device
	 *
	 * @throws Throwable
	 */
	public function __construct(
		string $username,
		string $password,
		Entities\Devices\IPhysicalDevice $device
	) {
		$this->id = Uuid\Uuid::uuid4();

		$this->username = $username;
		$this->password = $password;
		$this->device = $device;

		$device->setCredentials($this);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setUsername(string $username): void
	{
		$this->username = $username;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getUsername(): string
	{
		return $this->username;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setPassword(string $password): void
	{
		$this->password = $password;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDevice(): Entities\Devices\IPhysicalDevice
	{
		return $this->device;
	}

}
