<?php declare(strict_types = 1);

/**
 * NumberRowSchema.php
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
use Neomerx\JsonApi;

/**
 * Channel number configuration row entity schema
 *
 * @package         FastyBird:DevicesNode!
 * @subpackage      Schemas
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends RowSchema<Entities\Channels\Configuration\INumberRow>
 */
final class NumberRowSchema extends RowSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'devices-node/channel-configuration-number';

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Channels\Configuration\NumberRow::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * @param Entities\Channels\Configuration\INumberRow $row
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($row, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return array_merge((array) parent::getAttributes($row, $context), [
			'min'  => $row->getMin(),
			'max'  => $row->getMax(),
			'step' => $row->getStep(),
		]);
	}

}
