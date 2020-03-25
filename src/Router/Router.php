<?php declare(strict_types = 1);

/**
 * RouterFactory.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Router
 * @since          0.1.0
 *
 * @date           13.03.20
 */

namespace FastyBird\DevicesNode\Router;

use FastyBird\DevicesNode\Controllers;
use IPub\SlimRouter\Routing;
use Psr\Http\Message\ResponseFactoryInterface;

/**
 * Node router configuration
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Router
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class Router extends Routing\Router
{

	public const URL_ITEM_ID = 'id';

	public const URL_DEVICE_ID = 'device';
	public const URL_CHANNEL_ID = 'channel';

	public const RELATION_ENTITY = 'relationEntity';

	/** @var Controllers\DevicesV1Controller */
	private $devicesV1Controller;

	/** @var Controllers\DeviceChildrenV1Controller */
	private $deviceChildrenV1Controller;

	/** @var Controllers\DevicePropertiesV1Controller */
	private $devicePropertiesV1Controller;

	/** @var Controllers\DeviceConfigurationV1Controller */
	private $deviceConfigurationV1Controller;

	/** @var Controllers\DeviceCredentialsV1Controller */
	private $deviceCredentialsV1Controller;

	/** @var Controllers\DeviceHardwareV1Controller */
	private $deviceHardwareV1Controller;

	/** @var Controllers\DeviceFirmwareV1Controller */
	private $deviceFirmwareV1Controller;

	/** @var Controllers\ChannelsV1Controller */
	private $channelsV1Controller;

	/** @var Controllers\ChannelPropertiesV1Controller */
	private $channelPropertiesV1Controller;

	/** @var Controllers\ChannelConfigurationV1Controller */
	private $channelConfigurationV1Controller;

	public function __construct(
		Controllers\DevicesV1Controller $devicesV1Controller,
		Controllers\DeviceChildrenV1Controller $deviceChildrenV1Controller,
		Controllers\DevicePropertiesV1Controller $devicePropertiesV1Controller,
		Controllers\DeviceConfigurationV1Controller $deviceConfigurationV1Controller,
		Controllers\DeviceCredentialsV1Controller $deviceCredentialsV1Controller,
		Controllers\DeviceHardwareV1Controller $deviceHardwareV1Controller,
		Controllers\DeviceFirmwareV1Controller $deviceFirmwareV1Controller,
		Controllers\ChannelsV1Controller $channelsV1Controller,
		Controllers\ChannelPropertiesV1Controller $channelPropertiesV1Controller,
		Controllers\ChannelConfigurationV1Controller $channelConfigurationV1Controller,
		?ResponseFactoryInterface $responseFactory = null
	) {
		parent::__construct($responseFactory, null);

		$this->devicesV1Controller = $devicesV1Controller;
		$this->deviceChildrenV1Controller = $deviceChildrenV1Controller;
		$this->devicePropertiesV1Controller = $devicePropertiesV1Controller;
		$this->deviceConfigurationV1Controller = $deviceConfigurationV1Controller;
		$this->deviceCredentialsV1Controller = $deviceCredentialsV1Controller;
		$this->deviceHardwareV1Controller = $deviceHardwareV1Controller;
		$this->deviceFirmwareV1Controller = $deviceFirmwareV1Controller;
		$this->channelsV1Controller = $channelsV1Controller;
		$this->channelPropertiesV1Controller = $channelPropertiesV1Controller;
		$this->channelConfigurationV1Controller = $channelConfigurationV1Controller;
	}

	/**
	 * @return void
	 */
	public function registerRoutes(): void
	{
		$this->group('/v1', function (Routing\RouteCollector $group): void {
			$group->group('/devices', function (Routing\RouteCollector $group): void {
				/**
				 * DEVICES
				 */
				$route = $group->get('', [$this->devicesV1Controller, 'index']);
				$route->setName('devices');

				$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->devicesV1Controller, 'read']);
				$route->setName('device');

				$group->post('', [$this->devicesV1Controller, 'create']);

				$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->devicesV1Controller, 'update']);

				$group->delete('/{' . self::URL_ITEM_ID . '}', [$this->devicesV1Controller, 'delete']);

				$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->devicesV1Controller, 'readRelationship']);
				$route->setName('device.relationship');
			});

			$group->group('/devices/{' . self::URL_DEVICE_ID . '}', function (Routing\RouteCollector $group): void {
				/**
				 * CHILDREN
				 */
				$route = $group->get('/children', [$this->deviceChildrenV1Controller, 'index']);
				$route->setName('device.children');

				/**
				 * DEVICE PROPERTIES
				 */
				$route = $group->get('/properties', [$this->devicePropertiesV1Controller, 'index']);
				$route->setName('device.properties');

				$route = $group->get('/properties/{' . self::URL_ITEM_ID . '}', [$this->devicePropertiesV1Controller, 'read']);
				$route->setName('device.property');

				$route = $group->get('/properties/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->devicePropertiesV1Controller, 'readRelationship']);
				$route->setName('device.property.relationship');

				/**
				 * DEVICE CONFIGURATION
				 */
				$route = $group->get('/configuration', [$this->deviceConfigurationV1Controller, 'index']);
				$route->setName('device.configuration.rows');

				$route = $group->get('/configuration/{' . self::URL_ITEM_ID . '}', [$this->deviceConfigurationV1Controller, 'read']);
				$route->setName('device.configuration.row');

				$route = $group->get('/configuration/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->deviceConfigurationV1Controller, 'readRelationship']);
				$route->setName('device.configuration.row.relationship');

				/**
				 * DEVICE CREDENTIALS
				 */
				$route = $group->get('/credentials', [$this->deviceCredentialsV1Controller, 'read']);
				$route->setName('device.credentials');

				$route = $group->get('/credentials/relationships/{' . self::RELATION_ENTITY . '}', [$this->deviceCredentialsV1Controller, 'readRelationship']);
				$route->setName('device.credentials.relationship');

				/**
				 * DEVICE HARDWARE
				 */
				$route = $group->get('/hardware', [$this->deviceHardwareV1Controller, 'read']);
				$route->setName('device.hardware');

				$route = $group->get('/hardware/relationships/{' . self::RELATION_ENTITY . '}', [$this->deviceHardwareV1Controller, 'readRelationship']);
				$route->setName('device.hardware.relationship');

				/**
				 * DEVICE FIRMWARE
				 */
				$route = $group->get('/firmware', [$this->deviceFirmwareV1Controller, 'read']);
				$route->setName('device.firmware');

				$route = $group->get('/firmware/relationships/{' . self::RELATION_ENTITY . '}', [$this->deviceFirmwareV1Controller, 'readRelationship']);
				$route->setName('device.firmware.relationship');

				$group->group('/channels', function (Routing\RouteCollector $group): void {
					/**
					 * CHANNELS
					 */
					$route = $group->get('', [$this->channelsV1Controller, 'index']);
					$route->setName('channels');

					$route = $group->get('/{' . self::URL_ITEM_ID . '}', [$this->channelsV1Controller, 'read']);
					$route->setName('channel');

					$group->patch('/{' . self::URL_ITEM_ID . '}', [$this->channelsV1Controller, 'update']);

					$route = $group->get('/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->channelsV1Controller, 'readRelationship']);
					$route->setName('channel.relationship');

					$group->group('/{' . self::URL_CHANNEL_ID . '}', function (Routing\RouteCollector $group): void {
						/**
						 * CHANNEL PROPERTIES
						 */
						$route = $group->get('/properties', [$this->channelPropertiesV1Controller, 'index']);
						$route->setName('channel.properties');

						$route = $group->get('/properties/{' . self::URL_ITEM_ID . '}', [$this->channelPropertiesV1Controller, 'read']);
						$route->setName('channel.property');

						$route = $group->get('/properties/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->channelPropertiesV1Controller, 'readRelationship']);
						$route->setName('channel.property.relationship');

						/**
						 * CHANNEL CONFIGURATION
						 */
						$route = $group->get('/configuration', [$this->channelConfigurationV1Controller, 'index']);
						$route->setName('channel.configuration.rows');

						$route = $group->get('/configuration/{' . self::URL_ITEM_ID . '}', [$this->channelConfigurationV1Controller, 'read']);
						$route->setName('channel.configuration.row');

						$route = $group->get('/configuration/{' . self::URL_ITEM_ID . '}/relationships/{' . self::RELATION_ENTITY . '}', [$this->channelConfigurationV1Controller, 'readRelationship']);
						$route->setName('channel.configuration.row.relationship');
					});
				});
			});
		});
	}

}
