<?php declare(strict_types = 1);

/**
 * ChannelPropertyMessageHandler.php
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
use FastyBird\NodeExchange\Consumers as NodeExchangeConsumers;
use FastyBird\NodeExchange\Exceptions as NodeExchangeExceptions;
use FastyBird\NodeMetadata;
use FastyBird\NodeMetadata\Loaders as NodeMetadataLoaders;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Channel property message consumer
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelPropertyMessageHandler implements NodeExchangeConsumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var Models\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var Models\Channels\IChannelRepository */
	private $channelRepository;

	/** @var Models\Channels\Properties\IPropertiesManager */
	private $propertiesManager;

	/** @var Models\States\Channels\IPropertiesManager */
	private $propertiesStatesManager;

	/** @var Models\States\Channels\IPropertyRepository */
	private $propertyStateRepository;

	/** @var NodeMetadataLoaders\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Channels\IChannelRepository $channelRepository,
		Models\Channels\Properties\IPropertiesManager $propertiesManager,
		Models\States\Channels\IPropertiesManager $propertiesStatesManager,
		Models\States\Channels\IPropertyRepository $propertyStateRepository,
		NodeMetadataLoaders\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;
		$this->propertiesManager = $propertiesManager;
		$this->propertiesStatesManager = $propertiesStatesManager;
		$this->propertyStateRepository = $propertyStateRepository;

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

		try {
			$findQuery = new Queries\FindChannelsQuery();
			$findQuery->forDevice($device);
			$findQuery->byChannel($message->offsetGet('channel'));

			$channel = $this->channelRepository->findOneBy($findQuery);

		} catch (Throwable $ex) {
			throw new NodeExchangeExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($channel === null) {
			$this->logger->error(sprintf('[CONSUMER] Device channel "%s" is not registered', $message->offsetGet('device')));

			return true;
		}

		$dataConsumed = false;

		try {
			switch ($routingKey) {
				case DevicesNode\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY:
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
						$property = $channel->findProperty($message->offsetGet('property'));

						if ($property !== null) {
							$this->propertiesManager->update($property, Utils\ArrayHash::from($toUpdate));

						} else {
							$toCreate = $this->getProperty($channel, $message->offsetGet('property'));

							$this->propertiesManager->create(Utils\ArrayHash::from(array_merge($toCreate, $toUpdate)));
						}

						$dataConsumed = true;
					}

					if (
						$message->offsetExists('expected')
						|| $message->offsetExists('value')
					) {
						$property = $channel->findProperty($message->offsetGet('property'));

						if ($property !== null) {
							$propertyState = $this->propertyStateRepository->findOne($property->getId());

							if ($message->offsetExists('expected')) {
								$message->offsetSet('pending', true);
							}

							if ($propertyState !== null) {
								if (
									$message->offsetExists('value')
									&& (string) $propertyState->getExpected() === (string) $message->offsetGet('value')
								) {
									$message->offsetSet('pending', false);
									$message->offsetSet('expected', null);
								}

								$this->propertiesStatesManager->updateState($propertyState, $property, $message);

								$dataConsumed = true;
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

		if ($dataConsumed) {
			$this->logger->info('[CONSUMER] Successfully consumed entity message');
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSchema(string $routingKey, string $origin): ?string
	{
		switch ($routingKey) {
			case DevicesNode\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY:
				if ($origin === DevicesNode\Constants::NODE_MQTT_ORIGIN) {
					return $this->schemaLoader->load(NodeMetadata\Constants::RESOURCES_FOLDER . '/schemas/mqtt-node/data.channel.property.json');

				} elseif (
					$origin === DevicesNode\Constants::NODE_TRIGGERS_ORIGIN
					|| $origin === DevicesNode\Constants::NODE_WEBSOCKETS_ORIGIN
				) {
					return $this->schemaLoader->load(NodeMetadata\Constants::RESOURCES_FOLDER . '/schemas/data/data.channel.property.json');
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
	 * @param Entities\Channels\IChannel $channel
	 * @param string $property
	 *
	 * @return mixed[]
	 */
	private function getProperty(
		Entities\Channels\IChannel $channel,
		string $property
	): array {
		return [
			'channel'   => $channel,
			'name'      => $property,
			'property'  => $property,
			'settable'  => false,
			'queryable' => false,
		];
	}

}
