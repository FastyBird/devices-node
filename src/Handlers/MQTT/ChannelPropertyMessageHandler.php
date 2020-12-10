<?php declare(strict_types = 1);

/**
 * ChannelPropertyMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Handlers
 * @since          0.1.0
 *
 * @date           04.12.20
 */

namespace FastyBird\DevicesNode\Handlers\MQTT;

use Doctrine\Common;
use Doctrine\DBAL;
use Doctrine\DBAL\Connection;
use FastyBird\CouchDbStoragePlugin\Models as CouchDbStoragePluginModels;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\MqttPlugin\Entities as MqttPluginEntities;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Device channel property MQTT message handler
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Handlers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelPropertyMessageHandler
{

	use Nette\SmartObject;
	use TPropertyMessageHandler;

	/** @var DevicesModuleModels\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var DevicesModuleModels\Channels\IChannelRepository */
	private $channelRepository;

	/** @var DevicesModuleModels\Channels\Properties\IPropertiesManager */
	private $propertiesManager;

	/** @var CouchDbStoragePluginModels\IPropertiesManager */
	private $propertiesStatesManager;

	/** @var CouchDbStoragePluginModels\IPropertyRepository */
	private $propertyStateRepository;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		DevicesModuleModels\Devices\IDeviceRepository $deviceRepository,
		DevicesModuleModels\Channels\IChannelRepository $channelRepository,
		DevicesModuleModels\Channels\Properties\IPropertiesManager $propertiesManager,
		CouchDbStoragePluginModels\IPropertiesManager $propertiesStatesManager,
		CouchDbStoragePluginModels\IPropertyRepository $propertyStateRepository,
		Common\Persistence\ManagerRegistry $managerRegistry,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;
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
		MqttPluginEntities\ChannelProperty $entity
	): void {
		try {
			$findQuery = new DevicesModuleQueries\FindDevicesQuery();
			$findQuery->byIdentifier($entity->getDevice());

			$device = $this->deviceRepository->findOneBy($findQuery);

		} catch (Throwable $ex) {
			throw new Exceptions\InvalidStateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($device === null) {
			$this->logger->error(sprintf('[FB:NODE:MQTT] Device "%s" is not registered', $entity->getDevice()));

			return;
		}

		try {
			$findQuery = new DevicesModuleQueries\FindChannelsQuery();
			$findQuery->forDevice($device);
			$findQuery->byChannel($entity->getChannel());

			$channel = $this->channelRepository->findOneBy($findQuery);

		} catch (Throwable $ex) {
			throw new Exceptions\InvalidStateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($channel === null) {
			$this->logger->error(sprintf('[FB:NODE:MQTT] Device channel "%s" is not registered', $entity->getChannel()));

			return;
		}

		$property = $channel->findProperty($entity->getProperty());

		if ($property === null) {
			$this->logger->error(sprintf('[FB:NODE:MQTT] Property "%s" is not registered', $entity->getProperty()));

			return;
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$toUpdate = $this->handlePropertyConfiguration($entity);

			$this->propertiesManager->update($property, Utils\ArrayHash::from($toUpdate));

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
