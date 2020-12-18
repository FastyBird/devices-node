<?php declare(strict_types = 1);

/**
 * ServerBeforeStartHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Events
 * @since          0.1.0
 *
 * @date           26.07.20
 */

namespace FastyBird\DevicesNode\Events;

use FastyBird\RabbitMqPlugin;
use FastyBird\WebServer;
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

	/** @var RabbitMqPlugin\Exchange */
	private RabbitMqPlugin\Exchange $exchange;

	public function __construct(
		RabbitMqPlugin\Exchange $exchange
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
		try {
			$this->exchange->initializeAsync();

		} catch (RabbitMqPlugin\Exceptions\TerminateException $ex) {
			throw new WebServer\Exceptions\TerminateException($ex->getMessage(), $ex->getCode(), $ex);
		}
	}

}
