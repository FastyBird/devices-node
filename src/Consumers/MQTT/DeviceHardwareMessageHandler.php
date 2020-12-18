<?php declare(strict_types = 1);

/**
 * DeviceHardwareMessageHandler.php
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
 * Device hardware MQTT message handler
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Handlers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DeviceHardwareMessageHandler implements MqttPlugin\Consumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var DevicesModuleModels\Devices\IDeviceRepository */
	private DevicesModuleModels\Devices\IDeviceRepository $deviceRepository;

	/** @var DevicesModuleModels\Devices\PhysicalDevice\IHardwareManager */
	private DevicesModuleModels\Devices\PhysicalDevice\IHardwareManager $hardwareManager;

	/** @var Log\LoggerInterface */
	private Log\LoggerInterface $logger;

	/** @var Common\Persistence\ManagerRegistry */
	private Common\Persistence\ManagerRegistry $managerRegistry;

	public function __construct(
		DevicesModuleModels\Devices\IDeviceRepository $deviceRepository,
		DevicesModuleModels\Devices\PhysicalDevice\IHardwareManager $hardwareManager,
		Common\Persistence\ManagerRegistry $managerRegistry,
		?Log\LoggerInterface $logger = null
	) {
		$this->deviceRepository = $deviceRepository;
		$this->hardwareManager = $hardwareManager;

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
		if (!$entity instanceof MqttPlugin\Entities\Hardware) {
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
					MqttPlugin\Entities\Hardware::MAC_ADDRESS,
					MqttPlugin\Entities\Hardware::MANUFACTURER,
					MqttPlugin\Entities\Hardware::MODEL,
					MqttPlugin\Entities\Hardware::VERSION,
				] as $attribute
			) {
				if ($entity->getParameter() === $attribute) {
					$subResult = $this->setDeviceHardwareInfo($attribute, $entity->getValue());

					$toUpdate = array_merge($toUpdate, $subResult);
				}
			}

			if ($toUpdate !== []) {
				if ($device->getHardware() !== null) {
					$this->hardwareManager->update($device->getHardware(), Utils\ArrayHash::from($toUpdate));

				} else {
					$this->hardwareManager->create(Utils\ArrayHash::from(array_merge($toUpdate, ['device' => $device])));
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
	private function setDeviceHardwareInfo(
		string $parameter,
		string $value
	): array {
		$parametersMapping = [
			MqttPlugin\Entities\Hardware::MAC_ADDRESS  => 'macAddress',
			MqttPlugin\Entities\Hardware::MANUFACTURER => 'manufacturer',
			MqttPlugin\Entities\Hardware::MODEL        => 'model',
			MqttPlugin\Entities\Hardware::VERSION      => 'version',
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
