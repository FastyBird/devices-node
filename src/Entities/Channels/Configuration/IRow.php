<?php declare(strict_types = 1);

/**
 * IRow.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           26.10.18
 */

namespace FastyBird\DevicesNode\Entities\Channels\Configuration;

use FastyBird\DevicesNode\Entities;

/**
 * Channel configuration row entity interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IRow extends Entities\IRow
{

	/**
	 * @return Entities\Channels\IChannel
	 */
	public function getChannel(): Entities\Channels\IChannel;

}
