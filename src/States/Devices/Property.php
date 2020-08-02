<?php declare(strict_types = 1);

/**
 * Property.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     States
 * @since          0.1.0
 *
 * @date           03.03.20
 */

namespace FastyBird\DevicesNode\States\Devices;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\States;
use FastyBird\DevicesNode\Types;
use PHPOnCouch;

/**
 * Device property state
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     States
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class Property extends States\Property implements IProperty
{

	/** @var Entities\Devices\Properties\IProperty */
	private $property;

	public function __construct(
		string $id,
		PHPOnCouch\CouchDocument $document,
		Entities\Devices\Properties\IProperty $entity
	) {
		parent::__construct($id, $document);

		$this->property = $entity;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDatatype(): ?Types\DatatypeType
	{
		return $this->property->getDatatype();
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFormat()
	{
		return $this->property->getFormat();
	}

}
