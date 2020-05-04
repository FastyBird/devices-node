<?php declare(strict_types = 1);

/**
 * ChannelControlMessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
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
use FastyBird\JsonSchemas;
use FastyBird\JsonSchemas\Loaders as JsonSchemasLoaders;
use FastyBird\NodeLibs\Consumers as NodeLibsConsumers;
use FastyBird\NodeLibs\Exceptions as NodeLibsExceptions;
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
final class ChannelControlMessageHandler implements NodeLibsConsumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var Models\Devices\IDeviceRepository */
	private $deviceRepository;

	/** @var Models\Channels\IChannelRepository */
	private $channelRepository;

	/** @var Models\Channels\Controls\IControlsManager */
	private $controlsManager;

	/** @var Models\Channels\Configuration\IRowsManager */
	private $rowsManager;

	/** @var JsonSchemasLoaders\ISchemaLoader */
	private $schemaLoader;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Channels\IChannelRepository $channelRepository,
		Models\Channels\Controls\IControlsManager $controlsManager,
		Models\Channels\Configuration\IRowsManager $rowsManager,
		JsonSchemasLoaders\ISchemaLoader $schemaLoader,
		Log\LoggerInterface $logger
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;
		$this->controlsManager = $controlsManager;
		$this->rowsManager = $rowsManager;

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

		$control = $this->getControl($channel, $message->offsetGet('control'));

		if ($control === null) {
			$this->logger->error(sprintf('[CONSUMER] Channel control "%s" could not be loaded', $message->offsetGet('control')));

			return true;
		}

		$result = true;

		try {
			switch ($routingKey) {
				case DevicesNode\Constants::RABBIT_MQ_CHANNELS_CONTROLS_DATA_ROUTING_KEY:
					if ($control->getName() === DevicesNode\Constants::CONTROL_CONFIG) {
						if ($message->offsetExists('schema')) {
							$subResult = $this->setConfigurationSchema($channel, $message->offsetGet('schema'));

							if (!$subResult) {
								$result = false;
							}
						}

						if ($message->offsetExists('value')) {
							$subResult = $this->setConfigurationValues($channel, $message->offsetGet('value'));

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
			case DevicesNode\Constants::RABBIT_MQ_CHANNELS_CONTROLS_DATA_ROUTING_KEY:
				return $this->schemaLoader->load(JsonSchemas\Constants::MQTT_NODE_FOLDER . DS . 'data.channel.control.json');

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
			DevicesNode\Constants::RABBIT_MQ_CHANNELS_CONTROLS_DATA_ROUTING_KEY,
		];
	}

	/**
	 * @param Entities\Channels\IChannel $channel
	 * @param Utils\ArrayHash $schema
	 *
	 * @return bool
	 */
	private function setConfigurationSchema(
		Entities\Channels\IChannel $channel,
		Utils\ArrayHash $schema
	): bool {
		$elements = [];

		$configurationRow = new Utils\ArrayHash();
		$configurationRow->channel = $channel;

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
							$configurationRow->entity = Entities\Channels\Configuration\NumberRow::class;

							foreach (['min', 'max', 'step', 'default'] as $field) {
								if ($row->offsetExists($field) && $row->offsetGet($field) !== null) {
									$configurationRow->{$field} = (float) $row->offsetGet($field);

								} else {
									$configurationRow->{$field} = null;
								}
							}
							break;

						case DevicesNode\Constants::DATA_TYPE_TEXT:
							$configurationRow->entity = Entities\Channels\Configuration\TextRow::class;

							if ($row->offsetExists('default') && $row->offsetGet('default') !== null) {
								$configurationRow->default = (string) $row->offsetGet('default');
							}
							break;

						case DevicesNode\Constants::DATA_TYPE_BOOLEAN:
							$configurationRow->entity = Entities\Channels\Configuration\BooleanRow::class;

							if ($row->offsetExists('default') && $row->offsetGet('default') !== null) {
								$configurationRow->default = (bool) $row->offsetGet('default');
							}
							break;

						case DevicesNode\Constants::DATA_TYPE_SELECT:
							$configurationRow->entity = Entities\Channels\Configuration\SelectRow::class;

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

					$configuration = $channel->findConfiguration($row->offsetGet('name'));

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
		foreach ($channel->getConfiguration() as $row) {
			if (!in_array($row->getName(), $elements, true)) {
				$this->rowsManager->delete($row);
			}
		}

		return true;
	}

	/**
	 * @param Entities\Channels\IChannel $channel
	 * @param Utils\ArrayHash $values
	 *
	 * @return bool
	 */
	private function setConfigurationValues(
		Entities\Channels\IChannel $channel,
		Utils\ArrayHash $values
	): bool {
		foreach ($values as $name => $value) {
			$configuration = $channel->findConfiguration($name);

			if ($configuration !== null) {
				$this->rowsManager->update($configuration, Utils\ArrayHash::from([
					'value' => $value !== null ? (string) $value : null,
				]));
			}
		}

		return true;
	}

	/**
	 * @param Entities\Channels\IChannel $channel
	 * @param string $control
	 *
	 * @return Entities\Channels\Controls\IControl|null
	 */
	private function getControl(
		Entities\Channels\IChannel $channel,
		string $control
	): ?Entities\Channels\Controls\IControl {
		if ($channel->hasControl($control)) {
			return $channel->getControl($control);
		}

		return $this->controlsManager->create(Utils\ArrayHash::from([
			'channel' => $channel,
			'name'    => $control,
		]));
	}

}
