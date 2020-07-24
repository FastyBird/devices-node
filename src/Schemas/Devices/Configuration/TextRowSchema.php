<?php declare(strict_types = 1);

/**
 * TextRowSchema.php
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

namespace FastyBird\DevicesNode\Schemas\Devices\Configuration;

use FastyBird\DevicesNode\Entities;

/**
 * Device text configuration row entity schema constructor
 *
 * @package         FastyBird:DevicesNode!
 * @subpackage      Schemas
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends RowSchema<Entities\Devices\Configuration\ITextRow>
 */
final class TextRowSchema extends RowSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'devices-node/device-configuration-text';

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Devices\Configuration\TextRow::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

}
