<?php declare(strict_types = 1);

/**
 * State.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     States
 * @since          0.1.0
 *
 * @date           08.03.20
 */

namespace FastyBird\DevicesNode\States;

use FastyBird\DevicesNode\Exceptions;
use Nette;
use PHPOnCouch;
use Ramsey\Uuid;

/**
 * Base state
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     States
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class State implements IState
{

	use Nette\SmartObject;

	/** @var Uuid\UuidInterface */
	private $id;

	/** @var PHPOnCouch\CouchDocument */
	private $document;

	public function __construct(
		string $id,
		PHPOnCouch\CouchDocument $document
	) {
		if (!Uuid\Uuid::isValid($id)) {
			throw new Exceptions\InvalidStateException('Provided state id is not valid');
		}

		$this->id = Uuid\Uuid::fromString($id);

		$this->document = $document;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getId(): Uuid\UuidInterface
	{
		return $this->id;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getDocument(): PHPOnCouch\CouchDocument
	{
		return $this->document;
	}

	/**
	 * {@inheritDoc}
	 */
	public function toArray(): array
	{
		return [
			'id' => $this->getId()->toString(),
		];
	}

}
