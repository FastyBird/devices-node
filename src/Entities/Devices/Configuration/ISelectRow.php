<?php declare(strict_types = 1);

/**
 * ISelectRow.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           01.11.18
 */

namespace FastyBird\DevicesNode\Entities\Devices\Configuration;

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
