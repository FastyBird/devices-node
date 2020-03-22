<?php declare(strict_types = 1);

/**
 * IEntity.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           03.02.18
 */

namespace FastyBird\DevicesNode\Entities;

use IPub\DoctrineCrud;
use Ramsey\Uuid;

/**
 * Doctrine identified entity interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IEntity extends DoctrineCrud\Entities\IEntity
{

	/**
	 * @return Uuid\UuidInterface
	 */
	public function getId(): Uuid\UuidInterface;

	/**
	 * @return string
	 */
	public function getPlainId(): string;

	/**
	 * @return mixed[]
	 */
	public function toArray(): array;

}
