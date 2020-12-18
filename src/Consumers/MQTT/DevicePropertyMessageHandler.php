<?php declare(strict_types = 1);

/**
 * DevicePropertyMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Handlers
 * @since          0.1.0
 *
 * @date           04.12.20
 */

namespace FastyBird\DevicesNode\Consumers\MQTT;

use Doctrine\Common;
use Doctrine\DBAL;
use Doctrine\DBAL\Connection;
use FastyBird\CouchDbStoragePlugin\Models as CouchDbStoragePluginModels;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\MqttPlugin;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Device property MQTT message handler
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Handlers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DevicePropertyMessageHandler implements MqttPlugin\Consumers\IMessageHandler
{

	use Nette\SmartObject;
	use TPropertyMessageHandler;

	/** @var DevicesModuleModels\Devices\IDeviceRepository */
	private DevicesModuleModels\Devices\IDeviceRepository $deviceRepository;

	/** @var DevicesModuleModels\Devices\Properties\IPropertiesManager */
	private DevicesModuleModels\Devices\Properties\IPropertiesManager $propertiesManager;

	/** @var CouchDbStoragePluginModels\IPropertiesManager */
	private CouchDbStoragePluginModels\IPropertiesManager $propertiesStatesManager;

	/** @var CouchDbStoragePluginModels\IPropertyRepository */
	private CouchDbStoragePluginModels\IPropertyRepository $propertyStateRepository;

	/** @var Common\Persistence\ManagerRegistry */
	private Common\Persistence\ManagerRegistry $managerRegistry;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	public function __construct(
		DevicesModuleModels\Devices\IDeviceRepository $deviceRepository,
		DevicesModuleModels\Devices\Properties\IPropertiesManager $propertiesManager,
		CouchDbStoragePluginModels\IPropertiesManager $propertiesStatesManager,
		CouchDbStoragePluginModels\IPropertyRepository $propertyStateRepository,
		Common\Persistence\ManagerRegistry $managerRegistry,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->propertiesManager = $propertiesManager;
		$this->propertiesStatesManager = $propertiesStatesManager;
		$this->propertyStateRepository = $propertyStateRepository;

		$this->managerRegistry = $managerRegistry;

		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws DBAL\ConnectionException
	 * @throws Exceptions\InvalidStateException
	 */
	public function process(
		MqttPlugin\Entities\IEntity $entity
	): bool {
		if (!$entity instanceof MqttPlugin\Entities\DeviceProperty) {
			return false;
		}

		try {
			$findQuery = new DevicesModuleQueries\FindDevicesQuery();
			$findQuery->byIdentifier($entity->getDevice());

			$device = $this->deviceRepository->findOneBy($findQuery);

		} catch (Throwable $ex) {
			throw new Exceptions\InvalidStateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($device === null) {
			$this->logger->error(sprintf('[FB:NODE:MQTT] Device "%s" is not registered', $entity->getDevice()));

			return false;
		}

		$property = $device->findProperty($entity->getProperty());

		if ($property === null) {
			$this->logger->error(sprintf('[FB:NODE:MQTT] Property "%s" is not registered', $entity->getProperty()));

			return false;
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$toUpdate = $this->handlePropertyConfiguration($entity);

			if ($toUpdate !== []) {
				$this->propertiesManager->update($property, Utils\ArrayHash::from($toUpdate));
			}

			// Commit all changes into database
			$this->getOrmConnection()->commit();

			if ($entity->getValue() !== 'N/A') {
				$propertyState = $this->propertyStateRepository->findOne($property->getId());

				// In case synchronization failed...
				if ($propertyState === null) {
					// ...create state in storage
					$this->propertiesStatesManager->create(
						$property->getId(),
						Utils\ArrayHash::from(array_merge(
							$property->toArray(),
							[
								'value'    => $entity->getValue(),
								'expected' => null,
								'pending'  => false,
							]
						))
					);

				} else {
					$this->propertiesStatesManager->updateState(
						$propertyState,
						Utils\ArrayHash::from([
							'value'    => $entity->getValue(),
							'expected' => null,
							'pending'  => false,
						])
					);
				}
			}

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			throw new Exceptions\InvalidStateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		return true;
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
