<?php declare(strict_types = 1);

/**
 * ServerAfterStartHandler.php
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

use FastyBird\MqttPlugin;
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
class ServerAfterStartHandler
{

	use Nette\SmartObject;

	/** @var MqttPlugin\Client */
	private MqttPlugin\Client $mqttClient;

	public function __construct(
		MqttPlugin\Client $mqttClient
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
