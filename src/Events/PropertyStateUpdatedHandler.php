<?php declare(strict_types = 1);

/**
 * PropertyStateUpdatedHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Events
 * @since          0.1.0
 *
 * @date           20.12.20
 */

namespace FastyBird\DevicesNode\Events;

use FastyBird\ApplicationExchange\Publisher as ApplicationExchangePublisher;
use FastyBird\DevicesModule;
use FastyBird\DevicesModule\Helpers as DevicesModuleHelpers;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\DevicesModule\Queries as DevicesModuleQueries;
use FastyBird\DevicesModule\States as DevicesModuleStates;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\MqttPlugin\Senders as MqttPluginSenders;
use Nette;

/**
 * After property state updated handler
 *
 * @package         FastyBird:DevicesNode!
 * @subpackage      Events
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
class PropertyStateUpdatedHandler
{

	use Nette\SmartObject;

	/** @var DevicesModuleHelpers\PropertyHelper */
	protected DevicesModuleHelpers\PropertyHelper $propertyHelper;

	/** @var DevicesModuleModels\Devices\Properties\IPropertyRepository */
	protected DevicesModuleModels\Devices\Properties\IPropertyRepository $devicePropertyRepository;

	/** @var DevicesModuleModels\Channels\Properties\IPropertyRepository */
	protected DevicesModuleModels\Channels\Properties\IPropertyRepository $channelPropertyRepository;

	/** @var ApplicationExchangePublisher\IPublisher */
	private ApplicationExchangePublisher\IPublisher $publisher;

	/** @var Nette\DI\Container */
	private Nette\DI\Container $di;

	public function __construct(
		DevicesModuleHelpers\PropertyHelper $propertyHelper,
		DevicesModuleModels\Devices\Properties\IPropertyRepository $devicePropertyRepository,
		DevicesModuleModels\Channels\Properties\IPropertyRepository $channelPropertyRepository,
		ApplicationExchangePublisher\IPublisher $publisher,
		Nette\DI\Container $di
	) {
		$this->propertyHelper = $propertyHelper;
		$this->devicePropertyRepository = $devicePropertyRepository;
		$this->channelPropertyRepository = $channelPropertyRepository;

		$this->publisher = $publisher;

		$this->di = $di;
	}

	/**
	 * @param DevicesModuleStates\IProperty $state
	 * @param DevicesModuleStates\IProperty $previous
	 *
	 * @return void
	 */
	public function __invoke(
		DevicesModuleStates\IProperty $state,
		DevicesModuleStates\IProperty $previous
	): void {
		$findProperty = new DevicesModuleQueries\FindDevicePropertiesQuery();
		$findProperty->byId($state->getId());

		// Try to find device property...
		$property = $this->devicePropertyRepository->findOneBy($findProperty);

		// ...state is not for device or state id is invalid...
		if ($property === null) {
			$findProperty = new DevicesModuleQueries\FindChannelPropertiesQuery();
			$findProperty->byId($state->getId());

			// ...try to find channel property
			$property = $this->channelPropertyRepository->findOneBy($findProperty);
		}

		if ($property === null) {
			throw new Exceptions\InvalidArgumentException('Property for provided state could not be found.');
		}

		if (array_key_exists(get_class($property), DevicesModule\Constants::MESSAGE_BUS_UPDATED_ENTITIES_ROUTING_KEYS_MAPPING)) {
			$routingKey = DevicesModule\Constants::MESSAGE_BUS_UPDATED_ENTITIES_ROUTING_KEYS_MAPPING[get_class($property)];

		} else {
			throw new Exceptions\InvalidArgumentException('Provided state is not supported by RabbitMQ exchange publisher.');
		}

		$this->publisher->publish($routingKey, array_merge(
			$state->toArray(),
			[
				'previous_value' => $previous->getValue(),
			],
			$property->toArray()
		));

		if ($state->getExpected() !== null && $state->isPending()) {
			$findDeviceProperty = new DevicesModuleQueries\FindDevicePropertiesQuery();
			$findDeviceProperty->byId($state->getId());

			$property = $this->devicePropertyRepository->findOneBy($findDeviceProperty);

			if ($property !== null) {
				$this->di->getByType(MqttPluginSenders\ISender::class)->sendDeviceProperty(
					$property->getDevice()->getIdentifier(),
					$property->getProperty(),
					(string) $this->propertyHelper->normalizeValue($property, $state->getExpected()),
					$property->getDevice()->getParent() !== null ? $property->getDevice()->getParent()->getIdentifier() : null
				)
					->done();

			} else {
				$findChannelProperty = new DevicesModuleQueries\FindChannelPropertiesQuery();
				$findChannelProperty->byId($state->getId());

				$property = $this->channelPropertyRepository->findOneBy($findChannelProperty);

				if ($property !== null) {
					$this->di->getByType(MqttPluginSenders\ISender::class)->sendChannelProperty(
						$property->getChannel()->getDevice()->getIdentifier(),
						$property->getChannel()->getChannel(),
						$property->getProperty(),
						(string) $this->propertyHelper->normalizeValue($property, $state->getExpected()),
						$property->getChannel()->getDevice()->getParent() !== null ? $property->getChannel()->getDevice()->getParent()->getIdentifier() : null
					)
						->done();
				}
			}
		}
	}

}
