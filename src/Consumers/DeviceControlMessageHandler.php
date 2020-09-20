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

use Doctrine\Common;
use Doctrine\DBAL;
use Doctrine\DBAL\Connection;
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
	use TControlMessageHandler;

	/** @var Models\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var Models\Devices\Configuration\IRowsManager */
	private $rowsManager;

	/** @var NodeMetadataLoaders\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Devices\Configuration\IRowsManager $rowsManager,
		NodeMetadataLoaders\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger,
		Common\Persistence\ManagerRegistry $managerRegistry
	) {
		$this->deviceRepository = $deviceRepository;
		$this->rowsManager = $rowsManager;

		$this->schemaLoader = $schemaLoader;
		$this->logger = $logger;
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws DBAL\ConnectionException
	 * @throws NodeExchangeExceptions\TerminateException
	 */
	public function process(
		string $routingKey,
		string $origin,
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

		$control = $device->getControl($message->offsetGet('control'));

		if ($control === null) {
			$this->logger->error(sprintf('[CONSUMER] Channel control "%s" could not be loaded', $message->offsetGet('control')));

			return true;
		}

		try {
			switch ($routingKey) {
				case DevicesNode\Constants::RABBIT_MQ_DEVICES_CONTROLS_DATA_ROUTING_KEY:
					// Start transaction connection to the database
					$this->getOrmConnection()->beginTransaction();

					if ($control->getName() === DevicesNode\Constants::CONTROL_CONFIG) {
						if ($message->offsetExists('schema')) {
							$this->setConfigurationSchema($device, $message->offsetGet('schema'));
						}

						if ($message->offsetExists('value')) {
							$this->setConfigurationValues($device, $message->offsetGet('value'));
						}
					}

					// Commit all changes into database
					$this->getOrmConnection()->commit();
					break;

				default:
					throw new Exceptions\InvalidStateException('Unknown routing key');
			}

		} catch (Exceptions\InvalidStateException $ex) {
			return false;

		} catch (Throwable $ex) {
			throw new NodeExchangeExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);

		} finally {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}
		}

		$this->logger->info('[CONSUMER] Successfully consumed entity message', [
			'message' => [
				'routingKey' => $routingKey,
				'origin'     => $origin,
			],
		]);

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
	 * @return void
	 */
	private function setConfigurationSchema(
		Entities\Devices\IDevice $device,
		Utils\ArrayHash $schema
	): void {
		$elements = [];

		$configurationRows = $this->handleControlConfigurationSchema($schema, true);

		foreach ($configurationRows as $configurationRow) {
			$configuration = $device->findConfiguration($configurationRow['configuration']);

			$configurationRow['device'] = $device;

			$elements[] = $configurationRow['configuration'];

			if ($configuration === null) {
				$this->rowsManager->create(Utils\ArrayHash::from($configurationRow));

			} else {
				$this->rowsManager->update($configuration, Utils\ArrayHash::from($configurationRow));
			}
		}

		// Process cleanup of unused config rows
		foreach ($device->getConfiguration() as $row) {
			if (!in_array($row->getConfiguration(), $elements, true)) {
				$this->rowsManager->delete($row);
			}
		}
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param Utils\ArrayHash $values
	 *
	 * @return void
	 */
	private function setConfigurationValues(
		Entities\Devices\IDevice $device,
		Utils\ArrayHash $values
	): void {
		foreach ($values as $name => $value) {
			$configuration = $device->findConfiguration($name);

			if ($configuration !== null) {
				$this->rowsManager->update($configuration, Utils\ArrayHash::from([
					'value' => $value !== null ? (string) $value : null,
				]));
			}
		}
	}

	/**
	 * @return Connection
	 */
	protected function getOrmConnection(): Connection
	{
		$connection = $this->managerRegistry->getConnection();

		if ($connection instanceof Connection) {
			return $connection;
		}

		throw new Exceptions\RuntimeException('Entity manager could not be loaded');
	}

}
