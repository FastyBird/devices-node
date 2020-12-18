<?php declare(strict_types = 1);

/**
 * DeviceFirmwareMessageHandler.php
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
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\MqttPlugin;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Device firmware MQTT message handler
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DeviceFirmwareMessageHandler implements MqttPlugin\Consumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var DevicesModuleModels\Devices\IDeviceRepository */
	private DevicesModuleModels\Devices\IDeviceRepository $deviceRepository;

	/** @var DevicesModuleModels\Devices\PhysicalDevice\IFirmwareManager */
	private DevicesModuleModels\Devices\PhysicalDevice\IFirmwareManager $firmwareManager;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	/** @var Common\Persistence\ManagerRegistry */
	private Common\Persistence\ManagerRegistry $managerRegistry;

	public function __construct(
		DevicesModuleModels\Devices\IDeviceRepository $deviceRepository,
		DevicesModuleModels\Devices\PhysicalDevice\IFirmwareManager $firmwareManager,
		Common\Persistence\ManagerRegistry $managerRegistry,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->firmwareManager = $firmwareManager;

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
		if (!$entity instanceof MqttPlugin\Entities\Firmware) {
			return false;
		}

		try {
			$findQuery = new DevicesModuleQueries\FindDevicesQuery();
			$findQuery->byIdentifier($entity->getDevice());

			$device = $this->deviceRepository->findOneBy($findQuery);

		} catch (Throwable $ex) {
			throw new Exceptions\InvalidStateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if (
			$device === null ||
			(
				!$device instanceof DevicesModuleEntities\Devices\LocalDevice &&
				!$device instanceof DevicesModuleEntities\Devices\NetworkDevice
			)
		) {
			$this->logger->error(sprintf('[FB:NODE:MQTT] Device "%s" is not registered', $entity->getDevice()));

			return false;
		}

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			$toUpdate = [];

			foreach (
				[
					MqttPlugin\Entities\Firmware::MANUFACTURER,
					MqttPlugin\Entities\Firmware::NAME,
					MqttPlugin\Entities\Firmware::VERSION,
				] as $attribute
			) {
				if ($entity->getParameter() === $attribute) {
					$subResult = $this->setDeviceFirmwareInfo($attribute, $entity->getValue());

					$toUpdate = array_merge($toUpdate, $subResult);
				}
			}

			if ($toUpdate !== []) {
				if ($device->getFirmware() !== null) {
					$this->firmwareManager->update($device->getFirmware(), Utils\ArrayHash::from($toUpdate));

				} else {
					$this->firmwareManager->create(Utils\ArrayHash::from(array_merge($toUpdate, ['device' => $device])));
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

	/**
	 * @param string $parameter
	 * @param string $value
	 *
	 * @return mixed[]
	 */
	private function setDeviceFirmwareInfo(
		string $parameter,
		string $value
	): array {
		$parametersMapping = [
			MqttPlugin\Entities\Firmware::MANUFACTURER => 'manufacturer',
			MqttPlugin\Entities\Firmware::NAME         => 'name',
			MqttPlugin\Entities\Firmware::VERSION      => 'version',
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
