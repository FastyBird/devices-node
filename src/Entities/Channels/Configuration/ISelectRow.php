<?php declare(strict_types = 1);

/**
 * ISelectRow.php
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

interface ISelectRow extends IRow
{

	/**
	 * @param mixed[] $values
	 *
	 * @return void
	 */
	public function setValues(array $values): void;

	/**
	 * @return mixed[]
	 */
	public function getValues(): array;

}
