<?php declare(strict_types = 1);

/**
 * ChannelPropertyMessageHandler.php
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
use FastyBird\NodeLibs\Consumers as NodeLibsConsumers;
use FastyBird\NodeLibs\Exceptions as NodeLibsExceptions;
use FastyBird\NodeLibs\Helpers as NodeLibsHelpers;
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
final class ChannelPropertyMessageHandler implements NodeLibsConsumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var Models\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var Models\Channels\IChannelRepository */
	private $channelRepository;

	/** @var Models\Channels\Properties\IPropertiesManager */
	private $propertiesManager;

	/** @var NodeLibsHelpers\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Channels\IChannelRepository $channelRepository,
		Models\Channels\Properties\IPropertiesManager $propertiesManager,
		NodeLibsHelpers\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;
		$this->propertiesManager = $propertiesManager;

		$this->schemaLoader = $schemaLoader;
		$this->logger = $logger;
	}

	/**
	 * {@inheritDoc}
	 *
	 * @throws NodeLibsExceptions\TerminateException
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
			throw new NodeLibsExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
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
			throw new NodeLibsExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($channel === null) {
			$this->logger->error(sprintf('[CONSUMER] Device channel "%s" is not registered', $message->offsetGet('device')));

			return true;
		}

		$result = true;

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

					} else {
						$result = false;
					}
					break;

				default:
					throw new Exceptions\InvalidStateException('Unknown routing key');
			}

		} catch (Exceptions\InvalidStateException $ex) {
			return false;

		} catch (Throwable $ex) {
			throw new NodeLibsExceptions\TerminateException('An error occurred: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}

		if ($result) {
			$this->logger->info('[CONSUMER] Successfully consumed entity message');
		}

		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getAllowedOrigin(string $routingKey)
	{
		return DevicesNode\Constants::NODE_MQTT_ORIGIN;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getSchema(string $routingKey): string
	{
		switch ($routingKey) {
			case DevicesNode\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY:
				return $this->schemaLoader->load('data.channel.property.json');

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
			return DevicesNode\Constants::RABBIT_MQ_CHANNELS_PARTS_BINDINGS_ROUTING_KEY;
		}

		return [
			DevicesNode\Constants::RABBIT_MQ_CHANNELS_PROPERTIES_DATA_ROUTING_KEY,
		];
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
