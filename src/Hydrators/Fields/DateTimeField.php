<?php declare(strict_types = 1);

/**
 * DateTime.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           07.03.18
 */

namespace FastyBird\DevicesNode\Hydrators\Fields;

use DateTime;
use DateTimeInterface;
use IPub\JsonAPIDocument;
use Nette\Utils;

/**
 * Entity datetime field
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DateTimeField extends Field
{

	/** @var bool */
	private $isNullable = true;

	public function __construct(
		bool $isNullable,
		string $mappedName,
		string $fieldName,
		bool $isRequired,
		bool $isWritable
	) {
		parent::__construct($mappedName, $fieldName, $isRequired, $isWritable);

		$this->isNullable = $isNullable;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return DateTimeInterface|null
	 */
	public function getValue(JsonAPIDocument\Objects\IStandardObject $attributes): ?DateTimeInterface
	{
		$value = $attributes->get($this->getMappedName());

		if ($value !== null) {
			$date = Utils\DateTime::createFromFormat(DateTime::ATOM, (string) $value);

			if ($date instanceof DateTimeInterface && $date->format(DateTime::ATOM) === $value) {
				return $date;
			}
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function isNullable(): bool
	{
		return $this->isNullable;
	}

}
