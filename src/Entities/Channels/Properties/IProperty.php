<?php declare(strict_types = 1);

/**
 * IProperty.php
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

namespace FastyBird\DevicesNode\Entities\Channels\Properties;

use FastyBird\DevicesNode\Entities;

/**
 * Channel property entity interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IProperty extends Entities\IProperty
{

	public const COLOR_DATA_TYPE_FORMAT_RGB = 'rgb';
	public const COLOR_DATA_TYPE_FORMAT_HSV = 'hsv';

	/**
	 * @return Entities\Channels\IChannel
	 */
	public function getChannel(): Entities\Channels\IChannel;

}
