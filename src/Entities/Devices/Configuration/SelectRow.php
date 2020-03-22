<?php declare(strict_types = 1);

/**
 * SelectRow.php
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

use Doctrine\ORM\Mapping as ORM;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use Nette\Utils;

/**
 * @ORM\Entity
 */
class SelectRow extends Row implements ISelectRow
{

	/**
	 * @var mixed[]
	 * @IPubDoctrine\Crud(is="writable")
	 */
	protected $values = [];

	/**
	 * {@inheritDoc}
	 */
	public function setValues(array $values): void
	{
		$this->setParam('select_values', []);

		foreach ($values as $value) {
			$this->addValue($value);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getValues(): array
	{
		return $this->getParam('select_values', []);
	}

	/**
	 * @param Utils\ArrayHash $value
	 *
	 * @return void
	 */
	private function addValue(Utils\ArrayHash $value): void
	{
		$values = $this->getParam('select_values', []);

		if ($value->offsetExists('value') && $value->offsetExists('name')) {
			$values[] = [
				'value' => (string) $value->offsetGet('value'),
				'name'  => (string) $value->offsetGet('name'),
			];
		}

		$this->setParam('select_values', $values);
	}

}
