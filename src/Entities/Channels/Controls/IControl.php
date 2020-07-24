<?php declare(strict_types = 1);

/**
 * IControl.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           19.07.19
 */

namespace FastyBird\DevicesNode\Entities\Channels\Controls;

use FastyBird\DevicesNode\Entities;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use IPub\DoctrineTimestampable;

/**
 * Control settings entity interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IControl extends NodeDatabaseEntities\IEntity,
	DoctrineTimestampable\Entities\IEntityCreated, DoctrineTimestampable\Entities\IEntityUpdated
{

	/**
	 * @return Entities\Channels\IChannel
	 */
	public function getChannel(): Entities\Channels\IChannel;

	/**
	 * @return string
	 */
	public function getName(): string;

}
