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

namespace FastyBird\DevicesNode\Consumers\Bus;

use FastyBird\CouchDbStoragePlugin\Models as CouchDbStoragePluginModels;
use FastyBird\DevicesModule\Helpers as DevicesModuleHelpers;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\ModulesMetadata;
use FastyBird\ModulesMetadata\Loaders as ModulesMetadataLoaders;
use FastyBird\ModulesMetadata\Schemas as ModulesMetadataSchemas;
use FastyBird\MqttPlugin\Senders as MqttPluginSenders;
use FastyBird\RabbitMqPlugin\Exceptions as RabbitMqPluginExceptions;
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
final class DevicePropertyMessageHandler extends MessageHandler
{

	use TPropertyMessageHandler;

	/** @var DevicesModuleHelpers\PropertyHelper */
	protected DevicesModuleHelpers\PropertyHelper $propertyHelper;

	/** @var DevicesModuleModels\Devices\IDeviceRepository */
	private DevicesModuleModels\Devices\IDeviceRepository $deviceRepository;

	/** @var CouchDbStoragePluginModels\IPropertiesManager */
	private CouchDbStoragePluginModels\IPropertiesManager $propertiesStatesManager;

	/** @var CouchDbStoragePluginModels\IPropertyRepository */
	private CouchDbStoragePluginModels\IPropertyRepository $propertyStateRepository;

	/** @var MqttPluginSenders\ISender */
	private MqttPluginSenders\ISender $mqttV1sender;

	public function __construct(
		DevicesModuleModels\Devices\IDeviceRepository $deviceRepository,
		DevicesModuleHelpers\PropertyHelper $propertyHelper,
		CouchDbStoragePluginModels\IPropertiesManager $propertiesStatesManager,
		CouchDbStoragePluginModels\IPropertyRepository $propertyStateRepository,
		MqttPluginSenders\ISender $mqttV1sender,
		ModulesMetadataLoaders\ISchemaLoader $schemaLoader,
		ModulesMetadataSchemas\IValidator $validator,
		?Log\LoggerInterface $logger = null
	) {
		parent::__construct($schemaLoader, $validator, $logger);

		$this->deviceRepository = $deviceRepository;
		$this->propertyHelper = $propertyHelper;
		$this->propertiesStatesManager = $propertiesStatesManager;
		$this->propertyStateRepository = $propertyStateRepository;

		$this->mqttV1sender = $mqttV1sender;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws RabbitMqPluginExceptions\TerminateException
	 */
	public function process(
		string $routingKey,
		string $origin,
		string $payload
	): bool {
		$message = $this->parseMessage($routingKey, $origin, $payload);

		if ($message === null) {
			return true;
		}

		try {
			$findQuery = new DevicesModuleQueries\FindDevicesQuery();
			$findQuery->byIdentifier($message->offsetGet('device'));

			$device = $this->deviceRepository->findOneBy($findQuery);

		} catch (Throwable $ex) {
			throw new RabbitMqPluginExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($device === null) {
			$this->logger->error(sprintf('[FB:NODE:CONSUMER] Device "%s" is not registered', $message->offsetGet('device')));

			return true;
		}

		$property = $device->findProperty($message->offsetGet('property'));

		if ($property === null) {
			$this->logger->error(sprintf('[FB:NODE:CONSUMER] Property "%s" is not registered', $message->offsetGet('property')));

			return true;
		}

		try {
			switch ($routingKey) {
				case ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_PROPERTIES_DATA_ROUTING_KEY:
					// Property have to be configured & have to be settable
					if ($property->isSettable()) {
						$state = $this->propertyStateRepository->findOne($property->getId());

						// In case synchronization failed...
						if ($state === null) {
							// ...create state in storage
							$state = $this->propertiesStatesManager->create(
								$property->getId(),
								Utils\ArrayHash::from($property->toArray())
							);
						}

						$toUpdate = $this->handlePropertyState($property, $state, $message);

						$state = $this->propertiesStatesManager->updateState(
							$state,
							Utils\ArrayHash::from($toUpdate)
						);

						if ($state->getExpected() !== null && $state->isPending()) {
							$this->mqttV1sender->sendDeviceProperty(
								$property->getDevice()->getIdentifier(),
								$property->getProperty(),
								(string) $state->getExpected(),
								$property->getDevice()->getParent() !== null ? $property->getDevice()->getParent()->getIdentifier() : null
							)
								->done();
						}
					}
					break;

				default:
					throw new Exceptions\InvalidStateException('Unknown routing key');
			}

		} catch (Exceptions\InvalidStateException $ex) {
			return false;

		} catch (Throwable $ex) {
			throw new RabbitMqPluginExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		$this->logger->info('[FB:NODE:CONSUMER] Successfully consumed entity message', [
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
	protected function getSchemaFile(string $routingKey, string $origin): ?string
	{
		switch ($routingKey) {
			case ModulesMetadata\Constants::MESSAGE_BUS_DEVICES_PROPERTIES_DATA_ROUTING_KEY:
				if (
					$origin === ModulesMetadata\Constants::MODULE_TRIGGERS_ORIGIN
					|| $origin === ModulesMetadata\Constants::MODULE_UI_ORIGIN
				) {
					return ModulesMetadata\Constants::RESOURCES_FOLDER . '/schemas/data/data.device.property.json';
				}
		}

		return null;
	}

}
