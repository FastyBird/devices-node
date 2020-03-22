<?php declare(strict_types = 1);

/**
 * DevicePropertyMessageHandler.php
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
final class DevicePropertyMessageHandler implements NodeLibsConsumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var Models\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var Models\Devices\Properties\IPropertiesManager */
	private $propertiesManager;

	/** @var NodeLibsHelpers\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Devices\Properties\IPropertiesManager $propertiesManager,
		NodeLibsHelpers\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger
	) {
		$this->deviceRepository = $deviceRepository;
		$this->propertiesManager = $propertiesManager;

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

		$property = $this->getProperty($device, $message->offsetGet('property'));

		if ($property === null) {
			$this->logger->error(sprintf('[CONSUMER] Device property "%s" could not be loaded', $message->offsetGet('property')));

			return true;
		}

		$result = true;

		switch ($routingKey) {
			case DevicesNode\Constants::RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY:
				if ($message->offsetExists('name')) {
					$subResult = $this->setPropertyName($property, $message->offsetGet('name'));

					if (!$subResult) {
						$result = false;
					}
				}

				if ($message->offsetExists('settable')) {
					$subResult = $this->setPropertySettable($property, (bool) $message->offsetGet('settable'));

					if (!$subResult) {
						$result = false;
					}
				}

				if ($message->offsetExists('queryable')) {
					$subResult = $this->setPropertyQueryable($property, (bool) $message->offsetGet('queryable'));

					if (!$subResult) {
						$result = false;
					}
				}

				if ($message->offsetExists('datatype')) {
					$subResult = $this->setPropertyDatatype($property, $message->offsetGet('datatype'));

					if (!$subResult) {
						$result = false;
					}
				}

				if ($message->offsetExists('format')) {
					$subResult = $this->setPropertyFormat($property, $message->offsetGet('format'));

					if (!$subResult) {
						$result = false;
					}
				}

				if ($message->offsetExists('unit')) {
					$subResult = $this->setPropertyUnit($property, $message->offsetGet('unit'));

					if (!$subResult) {
						$result = false;
					}
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
			case DevicesNode\Constants::RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY:
				return $this->schemaLoader->load('data.device.property.json');

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
			DevicesNode\Constants::RABBIT_MQ_DEVICES_PROPERTIES_DATA_ROUTING_KEY,
		];
	}

	/**
	 * @param Entities\Devices\Properties\IProperty $property
	 * @param string $name
	 *
	 * @return bool
	 */
	private function setPropertyName(
		Entities\Devices\Properties\IProperty $property,
		string $name
	): bool {
		$this->propertiesManager->update($property, Utils\ArrayHash::from([
			'name' => $name,
		]));

		return true;
	}

	/**
	 * @param Entities\Devices\Properties\IProperty $property
	 * @param bool $settable
	 *
	 * @return bool
	 */
	private function setPropertySettable(
		Entities\Devices\Properties\IProperty $property,
		bool $settable
	): bool {
		$this->propertiesManager->update($property, Utils\ArrayHash::from([
			'settable' => $settable,
		]));

		return true;
	}

	/**
	 * @param Entities\Devices\Properties\IProperty $property
	 * @param bool $queryable
	 *
	 * @return bool
	 */
	private function setPropertyQueryable(
		Entities\Devices\Properties\IProperty $property,
		bool $queryable
	): bool {
		$this->propertiesManager->update($property, Utils\ArrayHash::from([
			'queryable' => $queryable,
		]));

		return true;
	}

	/**
	 * @param Entities\Devices\Properties\IProperty $property
	 * @param string $datatype
	 *
	 * @return bool
	 */
	private function setPropertyDatatype(
		Entities\Devices\Properties\IProperty $property,
		string $datatype
	): bool {
		$this->propertiesManager->update($property, Utils\ArrayHash::from([
			'datatype' => $datatype,
		]));

		return true;
	}

	/**
	 * @param Entities\Devices\Properties\IProperty $property
	 * @param string $format
	 *
	 * @return bool
	 */
	private function setPropertyFormat(
		Entities\Devices\Properties\IProperty $property,
		string $format
	): bool {
		$this->propertiesManager->update($property, Utils\ArrayHash::from([
			'format' => $format,
		]));

		return true;
	}

	/**
	 * @param Entities\Devices\Properties\IProperty $property
	 * @param string $unit
	 *
	 * @return bool
	 */
	private function setPropertyUnit(
		Entities\Devices\Properties\IProperty $property,
		string $unit
	): bool {
		$this->propertiesManager->update($property, Utils\ArrayHash::from([
			'unit' => $unit,
		]));

		return true;
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param string $property
	 *
	 * @return Entities\Devices\Properties\IProperty|null
	 */
	private function getProperty(
		Entities\Devices\IDevice $device,
		string $property
	): ?Entities\Devices\Properties\IProperty {
		if ($device->hasProperty($property)) {
			return $device->getProperty($property);
		}

		if (in_array($property, [
			Entities\Devices\Properties\IProperty::PROPERTY_IP_ADDRESS,
			Entities\Devices\Properties\IProperty::PROPERTY_STATUS_LED,
			Entities\Devices\Properties\IProperty::PROPERTY_SSID,
		], true)) {
			return $this->propertiesManager->create(Utils\ArrayHash::from([
				'device'    => $device,
				'name'      => $property,
				'property'  => $property,
				'settable'  => false,
				'queryable' => false,
				'datatype'  => Types\DatatypeType::DATA_TYPE_STRING,
			]));

		} elseif (in_array($property, [
			Entities\Devices\Properties\IProperty::PROPERTY_INTERVAL,
			Entities\Devices\Properties\IProperty::PROPERTY_UPTIME,
			Entities\Devices\Properties\IProperty::PROPERTY_FREE_HEAP,
			Entities\Devices\Properties\IProperty::PROPERTY_CPU_LOAD,
			Entities\Devices\Properties\IProperty::PROPERTY_VCC,
			Entities\Devices\Properties\IProperty::PROPERTY_RSSI,
		], true)) {
			return $this->propertiesManager->create(Utils\ArrayHash::from([
				'device'    => $device,
				'name'      => $property,
				'property'  => $property,
				'settable'  => false,
				'queryable' => false,
				'datatype'  => Types\DatatypeType::DATA_TYPE_INTEGER,
			]));
		}

		return null;
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
