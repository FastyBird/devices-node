<?php declare(strict_types = 1);

/**
 * ServerBeforeStartHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Events
 * @since          0.1.0
 *
 * @date           26.07.20
 */

namespace FastyBird\DevicesNode\Events;

use FastyBird\NodeExchange;
use Nette;
use Throwable;

/**
 * Http server before start handler
 *
 * @package         FastyBird:DevicesNode!
 * @subpackage      Events
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
class ServerBeforeStartHandler
{

	use Nette\SmartObject;

	/** @var NodeExchange\Exchange */
	private $exchange;

	public function __construct(
		NodeExchange\Exchange $exchange
	) {
		$this->exchange = $exchange;
	}

	/**
	 * @return void
	 *
	 * @throws Throwable
	 */
	public function __invoke(): void
	{
		$this->exchange->initializeAsync();
	}

}
