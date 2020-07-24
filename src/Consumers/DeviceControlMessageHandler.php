<?php declare(strict_types = 1);

/**
 * DeviceControlMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 * @since          0.1.0
 *
 * @date           25.03.20
 */

namespace FastyBird\DevicesNode\Consumers;

use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Queries;
use FastyBird\NodeExchange\Consumers as NodeExchangeConsumers;
use FastyBird\NodeExchange\Exceptions as NodeExchangeExceptions;
use FastyBird\NodeMetadata;
use FastyBird\NodeMetadata\Loaders as NodeMetadataLoaders;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Channel control message consumer
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DeviceControlMessageHandler implements NodeExchangeConsumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var Models\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var Models\Devices\Controls\IControlsManager */
	private $controlsManager;

	/** @var Models\Devices\Configuration\IRowsManager */
	private $rowsManager;

	/** @var NodeMetadataLoaders\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Devices\Controls\IControlsManager $controlsManager,
		Models\Devices\Configuration\IRowsManager $rowsManager,
		NodeMetadataLoaders\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger
	) {
		$this->deviceRepository = $deviceRepository;
		$this->controlsManager = $controlsManager;
		$this->rowsManager = $rowsManager;

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

		$control = $this->getControl($device, $message->offsetGet('control'));

		if ($control === null) {
			$this->logger->error(sprintf('[CONSUMER] Channel control "%s" could not be loaded', $message->offsetGet('control')));

			return true;
		}

		$result = true;

		try {
			switch ($routingKey) {
				case DevicesNode\Constants::RABBIT_MQ_DEVICES_CONTROLS_DATA_ROUTING_KEY:
					if ($control->getName() === DevicesNode\Constants::CONTROL_CONFIG) {
						if ($message->offsetExists('schema')) {
							$subResult = $this->setConfigurationSchema($device, $message->offsetGet('schema'));

							if (!$subResult) {
								$result = false;
							}
						}

						if ($message->offsetExists('value')) {
							$subResult = $this->setConfigurationValues($device, $message->offsetGet('value'));

							if (!$subResult) {
								$result = false;
							}
						}
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
				case DevicesNode\Constants::RABBIT_MQ_DEVICES_CONTROLS_DATA_ROUTING_KEY:
					return $this->schemaLoader->load(NodeMetadata\Constants::RESOURCES_FOLDER . '/schemas/mqtt-node/data.device.control.json');
			}
		}

		return null;
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param Utils\ArrayHash $schema
	 *
	 * @return bool
	 */
	private function setConfigurationSchema(
		Entities\Devices\IDevice $device,
		Utils\ArrayHash $schema
	): bool {
		$elements = [];

		$configurationRow = new Utils\ArrayHash();
		$configurationRow->device = $device;

		/** @var Utils\ArrayHash $row */
		foreach ($schema as $row) {
			try {
				if ($row->offsetExists('type')) {
					if ($row->offsetExists('name')) {
						$configurationRow->name = $row->offsetGet('name');

					} else {
						throw new Exceptions\InvalidStateException('Field name have to be set');
					}

					if ($row->offsetExists('title')) {
						$configurationRow->title = $row->offsetGet('title');

					} else {
						$configurationRow->title = null;
					}

					if ($row->offsetExists('comment')) {
						$configurationRow->comment = $row->offsetGet('comment');

					} else {
						$configurationRow->comment = null;
					}

					$configurationRow->default = null;
					$configurationRow->value = null;

					switch ($row->offsetGet('type')) {
						case DevicesNode\Constants::DATA_TYPE_NUMBER:
							$configurationRow->entity = Entities\Devices\Configuration\NumberRow::class;

							foreach (['min', 'max', 'step', 'default'] as $field) {
								if ($row->offsetExists($field) && $row->offsetGet($field) !== null) {
									$configurationRow->{$field} = (float) $row->offsetGet($field);

								} else {
									$configurationRow->{$field} = null;
								}
							}
							break;

						case DevicesNode\Constants::DATA_TYPE_TEXT:
							$configurationRow->entity = Entities\Devices\Configuration\TextRow::class;

							if ($row->offsetExists('default') && $row->offsetGet('default') !== null) {
								$configurationRow->default = (string) $row->offsetGet('default');
							}
							break;

						case DevicesNode\Constants::DATA_TYPE_BOOLEAN:
							$configurationRow->entity = Entities\Devices\Configuration\BooleanRow::class;

							if ($row->offsetExists('default') && $row->offsetGet('default') !== null) {
								$configurationRow->default = (bool) $row->offsetGet('default');
							}
							break;

						case DevicesNode\Constants::DATA_TYPE_SELECT:
							$configurationRow->entity = Entities\Devices\Configuration\SelectRow::class;

							if (
								$row->offsetExists('values')
								&& $row->offsetGet('values') instanceof Utils\ArrayHash
							) {
								$configurationRow->values = $row->offsetGet('values');

							} else {
								$configurationRow->values = [];
							}

							if ($row->offsetExists('default') && $row->offsetGet('default') !== null) {
								$configurationRow->default = (string) $row->offsetGet('default');
							}
							break;
					}

					$configuration = $device->findConfiguration($row->offsetGet('name'));

					$elements[] = $row->offsetGet('name');

					if ($configuration === null) {
						$this->rowsManager->create($configurationRow);

					} else {
						$this->rowsManager->update($configuration, $configurationRow);
					}
				}

			} catch (Exceptions\InvalidStateException $ex) {
				// Missing field name
			}
		}

		// Process cleanup of unused config rows
		foreach ($device->getConfiguration() as $row) {
			if (!in_array($row->getName(), $elements, true)) {
				$this->rowsManager->delete($row);
			}
		}

		return true;
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param Utils\ArrayHash $values
	 *
	 * @return bool
	 */
	private function setConfigurationValues(
		Entities\Devices\IDevice $device,
		Utils\ArrayHash $values
	): bool {
		foreach ($values as $name => $value) {
			$configuration = $device->findConfiguration($name);

			if ($configuration !== null) {
				$this->rowsManager->update($configuration, Utils\ArrayHash::from([
					'value' => $value !== null ? (string) $value : null,
				]));
			}
		}

		return true;
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param string $control
	 *
	 * @return Entities\Devices\Controls\IControl|null
	 */
	private function getControl(
		Entities\Devices\IDevice $device,
		string $control
	): ?Entities\Devices\Controls\IControl {
		if ($device->hasControl($control)) {
			return $device->getControl($control);
		}

		return $this->controlsManager->create(Utils\ArrayHash::from([
			'device' => $device,
			'name'   => $control,
		]));
	}

}
