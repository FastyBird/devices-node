<?php declare(strict_types = 1);

/**
 * BooleanRowSchema.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Schemas
 * @since          0.1.0
 *
 * @date           13.04.19
 */

namespace FastyBird\DevicesNode\Schemas\Channels\Configuration;

use FastyBird\DevicesNode\Entities;

/**
 * Channel boolean configuration row entity schema
 *
 * @package         FastyBird:DevicesNode!
 * @subpackage      Schemas
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends RowSchema<Entities\Channels\Configuration\IBooleanRow>
 */
final class BooleanRowSchema extends RowSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'devices-node/channel-configuration-boolean';

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Channels\Configuration\BooleanRow::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

}
