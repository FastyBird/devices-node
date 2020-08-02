<?php declare(strict_types = 1);

/**
 * PropertyUpdatedHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Events
 * @since          0.1.0
 *
 * @date           03.03.20
 */

namespace FastyBird\DevicesNode\Events;

use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Queries;
use FastyBird\DevicesNode\States;
use FastyBird\NodeExchange\Publishers as NodeExchangePublishers;
use Nette;

final class PropertyStateUpdatedHandler
{

	use Nette\SmartObject;

	private const ACTION = 'updated';

	/** @var Models\Devices\Properties\IPropertyRepository */
	private $devicePropertyRepository;

	/** @var Models\Channels\Properties\IPropertyRepository */
	private $channelPropertyRepository;

	/** @var NodeExchangePublishers\IRabbitMqPublisher */
	private $publisher;

	public function __construct(
		Models\Devices\Properties\IPropertyRepository $devicePropertyRepository,
		Models\Channels\Properties\IPropertyRepository $channelPropertyRepository,
		NodeExchangePublishers\IRabbitMqPublisher $publisher
	) {
		$this->devicePropertyRepository = $devicePropertyRepository;
		$this->channelPropertyRepository = $channelPropertyRepository;

		$this->publisher = $publisher;
	}

	/**
	 * @param States\IProperty $state
	 * @param States\IProperty $previous
	 *
	 * @return void
	 */
	public function __invoke(States\IProperty $state, States\IProperty $previous): void
	{
		if (array_key_exists(get_class($state), DevicesNode\Constants::RABBIT_MQ_ENTITIES_ROUTING_KEYS_MAPPING)) {
			$routingKey = DevicesNode\Constants::RABBIT_MQ_ENTITIES_ROUTING_KEYS_MAPPING[get_class($state)];

		} else {
			throw new Exceptions\InvalidArgumentException('Provided state is not supported by RabbitMQ exchange publisher.');
		}

		if ($state instanceof States\Devices\IProperty) {
			$findProperty = new Queries\FindDevicePropertiesQuery();
			$findProperty->byId($state->getId());

			$property = $this->devicePropertyRepository->findOneBy($findProperty);

		} elseif ($state instanceof States\Channels\IProperty) {
			$findProperty = new Queries\FindChannelPropertiesQuery();
			$findProperty->byId($state->getId());

			$property = $this->channelPropertyRepository->findOneBy($findProperty);

		} else {
			throw new Exceptions\InvalidArgumentException('Provided state is not supported.');
		}

		if ($property === null) {
			throw new Exceptions\InvalidArgumentException('Property for provided state could not be found.');
		}

		$routingKey = str_replace(DevicesNode\Constants::RABBIT_MQ_ENTITIES_ROUTING_KEY_ACTION_REPLACE_STRING, self::ACTION, $routingKey);

		$this->publisher->publish($routingKey, array_merge(
			$state->toArray(),
			[
				'previous_value' => $previous->getValue(),
			],
			$property->toArray()
		));
	}

}
