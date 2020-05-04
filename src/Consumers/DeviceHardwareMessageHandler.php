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
use FastyBird\JsonSchemas;
use FastyBird\JsonSchemas\Loaders as JsonSchemasLoaders;
use FastyBird\NodeLibs\Consumers as NodeLibsConsumers;
use FastyBird\NodeLibs\Exceptions as NodeLibsExceptions;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

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

	/** @var JsonSchemasLoaders\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Devices\PhysicalDevice\IHardwareManager $hardwareManager,
		JsonSchemasLoaders\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger
	) {
		$this->deviceRepository = $deviceRepository;
		$this->hardwareManager = $hardwareManager;

		$this->schemaLoader = $schemaLoader;
		$this->logger = $logger;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws NodeLibsExceptions\TerminateException
	 */
	public function process(
		string $routingKey,
		Utils\ArrayHash $message
	): bool {
		try {
			$findQuery = new Queries\FindPhysicalDevicesQuery();
			$findQuery->byIdentifier($message->offsetGet('device'));

			/** @var Entities\Devices\IPhysicalDevice|null $device */
			$device = $this->deviceRepository->findOneBy($findQuery, Entities\Devices\PhysicalDevice::class);

		} catch (Throwable $ex) {
			throw new NodeLibsExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($device === null) {
			$this->logger->error(sprintf('[CONSUMER] Device "%s" is not registered', $message->offsetGet('device')));

			return true;
		}

		$result = true;

		try {
			switch ($routingKey) {
				case DevicesNode\Constants::RABBIT_MQ_DEVICES_HARDWARE_DATA_ROUTING_KEY:
					$toUpdate = [];

					foreach (['mac-address', 'manufacturer', 'model', 'version'] as $attribute) {
						if ($message->offsetExists($attribute)) {
							$subResult = $this->setDeviceHardwareInfo($attribute, $message->offsetGet($attribute));

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

		} catch (Exceptions\InvalidStateException $ex) {
			return false;

		} catch (Throwable $ex) {
			throw new NodeLibsExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($result) {
			$this->logger->info('[CONSUMER] Successfully consumed entity message');
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAllowedOrigin(string $routingKey)
	{
		return DevicesNode\Constants::NODE_MQTT_ORIGIN;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSchema(string $routingKey): string
	{
		switch ($routingKey) {
			case DevicesNode\Constants::RABBIT_MQ_DEVICES_HARDWARE_DATA_ROUTING_KEY:
				return $this->schemaLoader->load(JsonSchemas\Constants::MQTT_NODE_FOLDER . DS . 'data.device.hardware.json');

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
	 * @param string $parameter
	 * @param string $value
	 *
	 * @return mixed[]
	 */
	private function setDeviceHardwareInfo(
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

}
