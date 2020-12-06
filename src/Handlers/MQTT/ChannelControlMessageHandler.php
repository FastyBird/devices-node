<?php declare(strict_types = 1);

/**
 * ChannelControlMessageHandler.php
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
use FastyBird\DevicesModule;
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\MqttPlugin\Entities as MqttPluginEntities;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Device control MQTT message handler
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelControlMessageHandler
{

	use Nette\SmartObject;
	use TControlMessageHandler;

	/** @var DevicesModuleModels\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var DevicesModuleModels\Channels\IChannelRepository */
	private $channelRepository;

	/** @var DevicesModuleModels\Channels\Configuration\IRowsManager */
	private $rowsManager;

	/** @var Log\LoggerInterface */
	private $logger;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	public function __construct(
		DevicesModuleModels\Devices\IDeviceRepository $deviceRepository,
		DevicesModuleModels\Channels\IChannelRepository $channelRepository,
		DevicesModuleModels\Channels\Configuration\IRowsManager $rowsManager,
		Common\Persistence\ManagerRegistry $managerRegistry,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;
		$this->rowsManager = $rowsManager;

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
		MqttPluginEntities\ChannelControl $entity
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

		$control = $channel->getControl($entity->getControl());

		if ($control === null) {
			$this->logger->error(sprintf('[FB:NODE:MQTT] Channel control "%s" could not be loaded', $entity->getControl()));

			return;
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if ($control->getName() === DevicesModule\Constants::CONTROL_CONFIG) {
				if ($entity->getSchema() !== null && is_array($entity->getSchema())) {
					$this->setConfigurationSchema($channel, Utils\ArrayHash::from($entity->getSchema()));
				}

				if ($entity->getValue() !== null && is_array($entity->getValue())) {
					$this->setConfigurationValues($channel, Utils\ArrayHash::from($entity->getValue()));
				}
			}

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			if ($this->getOrmConnection()->isTransactionActive()) {
				$this->getOrmConnection()->rollBack();
			}

			throw new Exceptions\InvalidStateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}
	}

	/**
	 * @param DevicesModuleEntities\Channels\IChannel $channel
	 * @param Utils\ArrayHash $schema
	 *
	 * @return void
	 */
	private function setConfigurationSchema(
		DevicesModuleEntities\Channels\IChannel $channel,
		Utils\ArrayHash $schema
	): void {
		$elements = [];

		$configurationRows = $this->handleControlConfigurationSchema($schema, false);

		foreach ($configurationRows as $configurationRow) {
			$configuration = $channel->findConfiguration($configurationRow['configuration']);

			$configurationRow['channel'] = $channel;

			$elements[] = $configurationRow['configuration'];

			if ($configuration === null) {
				$this->rowsManager->create(Utils\ArrayHash::from($configurationRow));

			} else {
				$this->rowsManager->update($configuration, Utils\ArrayHash::from($configurationRow));
			}
		}

		// Process cleanup of unused config rows
		foreach ($channel->getConfiguration() as $row) {
			if (!in_array($row->getConfiguration(), $elements, true)) {
				$this->rowsManager->delete($row);
			}
		}
	}

	/**
	 * @param DevicesModuleEntities\Channels\IChannel $channel
	 * @param Utils\ArrayHash $values
	 *
	 * @return void
	 */
	private function setConfigurationValues(
		DevicesModuleEntities\Channels\IChannel $channel,
		Utils\ArrayHash $values
	): void {
		foreach ($values as $name => $value) {
			$configuration = $channel->findConfiguration($name);

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