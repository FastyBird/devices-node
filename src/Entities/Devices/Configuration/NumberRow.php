<?php declare(strict_types = 1);

/**
 * NumberRow.php
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

/**
 * @ORM\Entity
 */
class NumberRow extends Row implements INumberRow
{

	/**
	 * @var float|null
	 * @IPubDoctrine\Crud(is="writable")
	 */
	protected $min = null;

	/**
	 * @var float|null
	 * @IPubDoctrine\Crud(is="writable")
	 */
	protected $max = null;

	/**
	 * @var float|null
	 * @IPubDoctrine\Crud(is="writable")
	 */
	protected $step = null;

	/**
	 * {@inheritDoc}
	 */
	public function setMin(?float $min): void
	{
		$this->setParam('min_value', $min);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMin(): ?float
	{
		return $this->getParam('min_value', null);
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasMin(): bool
	{
		return $this->getParam('min_value', null) !== null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setMax(?float $max): void
	{
		$this->setParam('max_value', $max);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getMax(): ?float
	{
		return $this->getParam('max_value', null);
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasMax(): bool
	{
		return $this->getParam('max_value', null) !== null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function setStep(?float $step): void
	{
		$this->setParam('step_value', $step);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getStep(): ?float
	{
		return $this->getParam('step_value', null);
	}

	/**
	 * {@inheritDoc}
	 */
	public function hasStep(): bool
	{
		return $this->getParam('step_value', null) !== null;
	}

}
