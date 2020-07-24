<?php declare(strict_types = 1);

/**
 * DevicePropertyMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
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
use FastyBird\NodeExchange\Consumers as NodeExchangeConsumers;
use FastyBird\NodeExchange\Exceptions as NodeExchangeExceptions;
use FastyBird\NodeMetadata;
use FastyBird\NodeMetadata\Loaders as NodeMetadataLoaders;
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
final class DevicePropertyMessageHandler implements NodeExchangeConsumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var Models\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var Models\Devices\Properties\IPropertiesManager */
	private $propertiesManager;

	/** @var NodeMetadataLoaders\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Devices\Properties\IPropertiesManager $propertiesManager,
		NodeMetadataLoaders\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger
	) {
		$this->deviceRepository = $deviceRepository;
		$this->propertiesManager = $propertiesManager;

		$this->schemaLoader = $schemaLoader;
		$this->logger = $logger;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws NodeExchangeExceptions\TerminateException
	 */
	public function process(
		string $routingKey,
		Utils\ArrayHash $message
	): bool {
		try {
			$findQuery = new Queries\FindDevicesQuery();
			$findQuery->byIdentifier($message->offsetGet('device'));

			$device = $this->deviceRepository->findOneBy($findQuery);

		} catch (Throwable $ex) {
			throw new NodeExchangeExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($device === null) {
			$this->logger->error(sprintf('[CONSUMER] Device "%s" is not registered', $message->offsetGet('device')));

			return true;
		}

		$result = true;

		try {
			switch ($routingKey) {
				case DevicesNode\Constants::RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY:
					$toUpdate = [];

					if ($message->offsetExists('name')) {
						$subResult = $this->setPropertyName($message->offsetGet('name'));

						$toUpdate = array_merge($toUpdate, $subResult);
					}

					if ($message->offsetExists('settable')) {
						$subResult = $this->setPropertySettable((bool) $message->offsetGet('settable'));

						$toUpdate = array_merge($toUpdate, $subResult);
					}

					if ($message->offsetExists('queryable')) {
						$subResult = $this->setPropertyQueryable((bool) $message->offsetGet('queryable'));

						$toUpdate = array_merge($toUpdate, $subResult);
					}

					if ($message->offsetExists('datatype')) {
						$subResult = $this->setPropertyDatatype($message->offsetGet('datatype'));

						$toUpdate = array_merge($toUpdate, $subResult);
					}

					if ($message->offsetExists('format')) {
						$subResult = $this->setPropertyFormat($message->offsetGet('format'));

						$toUpdate = array_merge($toUpdate, $subResult);
					}

					if ($message->offsetExists('unit')) {
						$subResult = $this->setPropertyUnit($message->offsetGet('unit'));

						$toUpdate = array_merge($toUpdate, $subResult);
					}

					if ($toUpdate !== []) {
						$property = $device->findProperty($message->offsetGet('property'));

						if ($property !== null) {
							$this->propertiesManager->update($property, Utils\ArrayHash::from($toUpdate));

						} else {
							$toCreate = $this->getProperty($device, $message->offsetGet('property'));

							if ($toCreate === null) {
								$this->logger->error(sprintf('[CONSUMER] Device property "%s" could not be initialized', $message->offsetGet('property')));

								return true;
							}

							$this->propertiesManager->create(Utils\ArrayHash::from(array_merge($toCreate, $toUpdate)));
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
			throw new NodeExchangeExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($result) {
			$this->logger->info('[CONSUMER] Successfully consumed entity message');
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSchema(string $routingKey, string $origin): ?string
	{
		if ($origin === DevicesNode\Constants::NODE_MQTT_ORIGIN) {
			switch ($routingKey) {
				case DevicesNode\Constants::RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY:
					return $this->schemaLoader->load(NodeMetadata\Constants::RESOURCES_FOLDER . '/schemas/mqtt-node/data.device.property.json');
			}
		}

		return null;
	}

	/**
	 * @param string $name
	 *
	 * @return mixed[]
	 */
	private function setPropertyName(
		string $name
	): array {
		return [
			'name' => $name,
		];
	}

	/**
	 * @param bool $settable
	 *
	 * @return mixed[]
	 */
	private function setPropertySettable(
		bool $settable
	): array {
		return [
			'settable' => $settable,
		];
	}

	/**
	 * @param bool $queryable
	 *
	 * @return mixed[]
	 */
	private function setPropertyQueryable(
		bool $queryable
	): array {
		return [
			'queryable' => $queryable,
		];
	}

	/**
	 * @param string|null $datatype
	 *
	 * @return mixed[]
	 */
	private function setPropertyDatatype(
		?string $datatype
	): array {
		return [
			'datatype' => $datatype,
		];
	}

	/**
	 * @param string|null $format
	 *
	 * @return mixed[]
	 */
	private function setPropertyFormat(
		?string $format
	): array {
		return [
			'format' => $format,
		];
	}

	/**
	 * @param string|null $unit
	 *
	 * @return mixed[]
	 */
	private function setPropertyUnit(
		?string $unit
	): array {
		return [
			'unit' => $unit,
		];
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param string $property
	 *
	 * @return mixed[]|null
	 */
	private function getProperty(
		Entities\Devices\IDevice $device,
		string $property
	): ?array {
		if (in_array($property, [
			Entities\Devices\Properties\IProperty::PROPERTY_IP_ADDRESS,
			Entities\Devices\Properties\IProperty::PROPERTY_STATUS_LED,
			Entities\Devices\Properties\IProperty::PROPERTY_SSID,
		], true)) {
			return [
				'device'    => $device,
				'name'      => $property,
				'property'  => $property,
				'settable'  => false,
				'queryable' => false,
				'datatype'  => Types\DatatypeType::DATA_TYPE_STRING,
			];

		} elseif (in_array($property, [
			Entities\Devices\Properties\IProperty::PROPERTY_INTERVAL,
			Entities\Devices\Properties\IProperty::PROPERTY_UPTIME,
			Entities\Devices\Properties\IProperty::PROPERTY_FREE_HEAP,
			Entities\Devices\Properties\IProperty::PROPERTY_CPU_LOAD,
			Entities\Devices\Properties\IProperty::PROPERTY_VCC,
			Entities\Devices\Properties\IProperty::PROPERTY_RSSI,
		], true)) {
			return [
				'device'    => $device,
				'name'      => $property,
				'property'  => $property,
				'settable'  => false,
				'queryable' => false,
				'datatype'  => Types\DatatypeType::DATA_TYPE_INTEGER,
			];
		}

		return null;
	}

}
