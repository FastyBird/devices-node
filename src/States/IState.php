<?php declare(strict_types = 1);

/**
 * IState.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     States
 * @since          0.1.0
 *
 * @date           04.03.20
 */

namespace FastyBird\DevicesNode\States;

use PHPOnCouch;
use Ramsey\Uuid;

/**
 * Base state interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     States
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IState
{

	/**
	 * @return Uuid\UuidInterface
	 */
	public function getId(): Uuid\UuidInterface;

	/**
	 * @return PHPOnCouch\CouchDocument
	 */
	public function getDocument(): PHPOnCouch\CouchDocument;

	/**
	 * @return mixed[]
	 */
	public function toArray(): array;

}
