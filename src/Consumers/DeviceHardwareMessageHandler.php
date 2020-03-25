<?php declare(strict_types = 1);

/**
 * DeviceHardwareMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 * @since          0.1.0
 *
 * @date           19.03.20
 */

namespace FastyBird\DevicesNode\Consumers;

use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Queries;
use FastyBird\DevicesNode\Types;
use FastyBird\NodeLibs\Consumers as NodeLibsConsumers;
use FastyBird\NodeLibs\Helpers as NodeLibsHelpers;
use Nette;
use Nette\Utils;
use Psr\Log;

/**
 * Device message consumer
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DeviceHardwareMessageHandler implements NodeLibsConsumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var Models\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var Models\Devices\PhysicalDevice\IHardwareManager */
	private $hardwareManager;

	/** @var NodeLibsHelpers\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Devices\PhysicalDevice\IHardwareManager $hardwareManager,
		NodeLibsHelpers\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger
	) {
		$this->deviceRepository = $deviceRepository;
		$this->hardwareManager = $hardwareManager;

		$this->schemaLoader = $schemaLoader;
		$this->logger = $logger;
	}

	/**
	 * {@inheritDoc}
	 */
	public function process(
		string $routingKey,
		Utils\ArrayHash $message
	): bool {
		$findDevice = new Queries\FindPhysicalDevicesQuery();
		$findDevice->byIdentifier($message->offsetGet('device'));

		/** @var Entities\Devices\IPhysicalDevice|null $device */
		$device = $this->deviceRepository->findOneBy($findDevice, Entities\Devices\PhysicalDevice::class);

		if ($device === null) {
			$this->logger->error(sprintf('[CONSUMER] Device "%s" is not registered', $message->offsetGet('device')));

			return true;
		}

		// Check if device is in initialize mode
		if (!$this->isInitEnabled($device)) {
			$this->logger->info(sprintf('[CONSUMER] Device "%s" is in "%s" state and can\'t be updated', $message->offsetGet('device'), $device->getState()->getValue()));

			return true;
		}

		$result = true;

		switch ($routingKey) {
			case DevicesNode\Constants::RABBIT_MQ_DEVICES_HARDWARE_DATA_ROUTING_KEY:
				$toUpdate = [];

				foreach (['mac-address', 'manufacturer', 'model', 'version'] as $attribute) {
					if ($message->offsetExists($attribute)) {
						$subResult = $this->setDeviceHardwareInfo($device, $attribute, $message->offsetGet($attribute));

						$toUpdate = array_merge($toUpdate, $subResult);
					}
				}

				if ($toUpdate !== []) {
					if ($device->getHardware() !== null) {
						$this->hardwareManager->update($device->getHardware(), Utils\ArrayHash::from($toUpdate));

					} else {
						$this->hardwareManager->create(Utils\ArrayHash::from(array_merge($toUpdate, ['device' => $device])));
					}

				} else {
					$result = false;
				}
				break;

			default:
				throw new Exceptions\InvalidStateException('Unknown routing key');
		}

		if ($result) {
			$this->logger->info('[CONSUMER] Successfully consumed entity message');
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSchema(string $routingKey): string
	{
		switch ($routingKey) {
			case DevicesNode\Constants::RABBIT_MQ_DEVICES_HARDWARE_DATA_ROUTING_KEY:
				return $this->schemaLoader->load('data.device.hardware.json');

			default:
				throw new Exceptions\InvalidStateException('Unknown routing key');
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getRoutingKeys(bool $binding = false): array
	{
		if ($binding) {
			return DevicesNode\Constants::RABBIT_MQ_DEVICES_PARTS_BINDINGS_ROUTING_KEYS;
		}

		return [
			DevicesNode\Constants::RABBIT_MQ_DEVICES_HARDWARE_DATA_ROUTING_KEY,
		];
	}

	/**
	 * @param Entities\Devices\IPhysicalDevice $device
	 * @param string $parameter
	 * @param string $value
	 *
	 * @return mixed[]
	 */
	private function setDeviceHardwareInfo(
		Entities\Devices\IPhysicalDevice $device,
		string $parameter,
		string $value
	): array {
		$parametersMapping = [
			'mac-address'  => 'macAddress',
			'manufacturer' => 'manufacturer',
			'model'        => 'model',
			'version'      => 'version',
		];

		foreach ($parametersMapping as $key => $field) {
			if ($parameter === $key) {
				return [
					$field => $value,
				];
			}
		}

		return [];
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 *
	 * @return bool
	 */
	private function isInitEnabled(
		Entities\Devices\IDevice $device
	): bool {
		return $device->getState()->equalsValue(Types\DeviceConnectionState::STATE_INIT);
	}

}
