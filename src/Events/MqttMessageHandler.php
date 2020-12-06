<?php declare(strict_types = 1);

/**
 * MqttMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Events
 * @since          0.1.0
 *
 * @date           04.12.20
 */

namespace FastyBird\DevicesNode\Events;

use FastyBird\DevicesNode\Handlers;
use FastyBird\MqttPlugin\Entities as MqttPluginEntities;
use FastyBird\WebServer;
use Nette;
use Psr\Log;
use Throwable;

/**
 * MQTT message received handler
 *
 * @package         FastyBird:DevicesNode!
 * @subpackage      Events
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class MqttMessageHandler
{

	use Nette\SmartObject;

	/** @var Handlers\MQTT\DeviceMessageHandler */
	private $deviceHandler;

	/** @var Handlers\MQTT\DeviceHardwareMessageHandler */
	private $deviceHardwareHandler;

	/** @var Handlers\MQTT\DeviceFirmwareMessageHandler */
	private $deviceFirmwareHandler;

	/** @var Handlers\MQTT\DevicePropertyMessageHandler */
	private $devicePropertyHandler;

	/** @var Handlers\MQTT\DeviceControlMessageHandler */
	private $deviceControlHandler;

	/** @var Handlers\MQTT\ChannelMessageHandler */
	private $channelHandler;

	/** @var Handlers\MQTT\ChannelPropertyMessageHandler */
	private $channelPropertyHandler;

	/** @var Handlers\MQTT\ChannelControlMessageHandler */
	private $channelControlHandler;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		Handlers\MQTT\DeviceMessageHandler $deviceHandler,
		Handlers\MQTT\DeviceHardwareMessageHandler $deviceHardwareHandler,
		Handlers\MQTT\DeviceFirmwareMessageHandler $deviceFirmwareHandler,
		Handlers\MQTT\DevicePropertyMessageHandler $devicePropertyHandler,
		Handlers\MQTT\DeviceControlMessageHandler $deviceControlHandler,
		Handlers\MQTT\ChannelMessageHandler $channelHandler,
		Handlers\MQTT\ChannelPropertyMessageHandler $channelPropertyHandler,
		Handlers\MQTT\ChannelControlMessageHandler $channelControlHandler,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceHandler = $deviceHandler;
		$this->deviceHardwareHandler = $deviceHardwareHandler;
		$this->deviceFirmwareHandler = $deviceFirmwareHandler;
		$this->devicePropertyHandler = $devicePropertyHandler;
		$this->deviceControlHandler = $deviceControlHandler;
		$this->channelHandler = $channelHandler;
		$this->channelPropertyHandler = $channelPropertyHandler;
		$this->channelControlHandler = $channelControlHandler;

		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * @param MqttPluginEntities\IEntity $entity
	 *
	 * @throws WebServer\Exceptions\TerminateException
	 */
	public function __invoke(
		MqttPluginEntities\IEntity $entity
	): void {
		try {
			if ($entity instanceof MqttPluginEntities\DeviceAttribute) {
				$this->deviceHandler->process($entity);

			} elseif ($entity instanceof MqttPluginEntities\DeviceProperty) {
				$this->devicePropertyHandler->process($entity);

			} elseif ($entity instanceof MqttPluginEntities\DeviceControl) {
				$this->deviceControlHandler->process($entity);

			} elseif ($entity instanceof MqttPluginEntities\Hardware) {
				$this->deviceHardwareHandler->process($entity);

			} elseif ($entity instanceof MqttPluginEntities\Firmware) {
				$this->deviceFirmwareHandler->process($entity);

			} elseif ($entity instanceof MqttPluginEntities\ChannelAttribute) {
				$this->channelHandler->process($entity);

			} elseif ($entity instanceof MqttPluginEntities\ChannelProperty) {
				$this->channelPropertyHandler->process($entity);

			} elseif ($entity instanceof MqttPluginEntities\ChannelControl) {
				$this->channelControlHandler->process($entity);
			}

		} catch (Throwable $ex) {
			$this->logger->error('[FB:NODE:MQTT] Received MQTT message could not be handled', [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
				'entity'    => $entity->toArray(),
			]);

			throw new WebServer\Exceptions\TerminateException($ex->getMessage(), $ex->getCode(), $ex);
		}
	}

}
