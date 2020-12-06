<?php declare(strict_types = 1);

/**
 * ServerStartHandler.php
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

use IPub\MQTTClient;
use Nette;
use Throwable;

/**
 * Http server start handler
 *
 * @package         FastyBird:DevicesNode!
 * @subpackage      Events
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
class ServerStartHandler
{

	use Nette\SmartObject;

	/** @var MQTTClient\Client\IClient */
	private $mqttClient;

	public function __construct(
		MQTTClient\Client\IClient $mqttClient
	) {
		$this->mqttClient = $mqttClient;
	}

	/**
	 * @return void
	 *
	 * @throws Throwable
	 */
	public function __invoke(): void
	{
		$this->mqttClient->connect();
	}

}
