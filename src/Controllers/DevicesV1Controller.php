<?php declare(strict_types = 1);

/**
 * DevicesController.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           13.04.19
 */

namespace FastyBird\DevicesNode\Controllers;

use Doctrine;
use FastyBird\DevicesNode\Controllers;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Hydrators;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Queries;
use FastyBird\DevicesNode\Router;
use FastyBird\DevicesNode\Schemas;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use IPub\DoctrineCrud\Exceptions as DoctrineCrudExceptions;
use Nette\Utils;
use Psr\Http\Message;
use Throwable;

/**
 * API devices controller
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class DevicesV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TDeviceFinder;

	/** @var Models\Devices\IDeviceRepository */
	protected $deviceRepository;

	/** @var Models\Devices\IDevicesManager */
	protected $devicesManager;

	/** @var Models\Channels\IChannelRepository */
	protected $channelRepository;

	/** @var Models\Channels\IChannelsManager */
	protected $channelsManager;

	/** @var Hydrators\Devices\PhysicalDeviceHydrator */
	private $physicalDeviceHydrator;

	/** @var string */
	protected $translationDomain = 'node.devices';

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Devices\IDevicesManager $devicesManager,
		Models\Channels\IChannelRepository $channelRepository,
		Models\Channels\IChannelsManager $channelsManager,
		Hydrators\Devices\PhysicalDeviceHydrator $physicalDeviceHydrator
	) {
		$this->deviceRepository = $deviceRepository;
		$this->devicesManager = $devicesManager;
		$this->channelRepository = $channelRepository;
		$this->channelsManager = $channelsManager;
		$this->physicalDeviceHydrator = $physicalDeviceHydrator;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 */
	public function index(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$findQuery = new Queries\FindDevicesQuery();

		$devices = $this->deviceRepository->getResultSet($findQuery);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($devices));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\JsonApiErrorException
	 */
	public function read(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$device = $this->findDevice($request->getAttribute(Router\Router::URL_ITEM_ID));

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($device));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 * @throws NodeWebServerExceptions\JsonApiErrorException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function create(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		if ($document->getResource()->getType() === Schemas\Devices\PhysicalDeviceSchema::SCHEMA_TYPE) {
			try {
				// Start transaction connection to the database
				$this->getOrmConnection()->beginTransaction();

				$device = $this->devicesManager->create($this->physicalDeviceHydrator->hydrateDevice($document->getResource(), $document->getIncluded()));

				// Commit all changes into database
				$this->getOrmConnection()->commit();

			} catch (NodeWebServerExceptions\IJsonApiException $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollback();

				throw $ex;

			} catch (DoctrineCrudExceptions\MissingRequiredFieldException $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollback();

				if ($ex->getField() === Schemas\Devices\PhysicalDeviceSchema::RELATIONSHIPS_CREDENTIALS) {
					$pointer = 'data/relationships/' . $ex->getField();

				} else {
					$pointer = 'data/attributes/' . $ex->getField();
				}

				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//node.base.messages.missingRequired.heading'),
					$this->translator->translate('//node.base.messages.missingRequired.message'),
					[
						'pointer' => $pointer,
					]
				);

			} catch (DoctrineCrudExceptions\EntityCreationException $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollback();

				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//node.base.messages.missingRequired.heading'),
					$this->translator->translate('//node.base.messages.missingRequired.message'),
					[
						'pointer' => 'data/attributes/' . $ex->getField(),
					]
				);

			} catch (Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollback();

				if (preg_match("%key '(?P<key>.+)_unique'%", $ex->getMessage(), $match)) {
					if (Utils\Strings::startsWith($match['key'], 'device_')) {
						throw new NodeWebServerExceptions\JsonApiErrorException(
							StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
							$this->translator->translate('//node.base.messages.uniqueConstraint.heading'),
							$this->translator->translate('//node.base.messages.uniqueConstraint.message'),
							[
								'pointer' => '/data/attributes/' . Utils\Strings::substring($match['key'], 7),
							]
						);
					}
				}

				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('//node.base.messages.uniqueConstraint.heading'),
					$this->translator->translate('//node.base.messages.uniqueConstraint.message')
				);

			} catch (Throwable $ex) {
				// Revert all changes when error occur
				$this->getOrmConnection()->rollback();

				// Log catched exception
				$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
					'exception' => [
						'message' => $ex->getMessage(),
						'code'    => $ex->getCode(),
					],
				]);

				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('messages.notCreated.heading'),
					$this->translator->translate('messages.notCreated.message')
				);
			}

			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($device));
		}

		throw new NodeWebServerExceptions\JsonApiErrorException(
			StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
			$this->translator->translate('messages.invalidType.heading'),
			$this->translator->translate('messages.invalidType.message'),
			[
				'pointer' => '/data/type',
			]
		);
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 * @throws NodeWebServerExceptions\JsonApiErrorException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function update(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$document = $this->createDocument($request);

		if ($request->getAttribute(Router\Router::URL_ITEM_ID) !== $document->getResource()->getIdentifier()->getId()) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.invalid.heading'),
				$this->translator->translate('//node.base.messages.invalid.message')
			);
		}

		$device = $this->findDevice($request->getAttribute(Router\Router::URL_ITEM_ID));

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			if (
				$document->getResource()->getType() === Schemas\Devices\PhysicalDeviceSchema::SCHEMA_TYPE
				&& $device instanceof Entities\Devices\IPhysicalDevice
			) {
				$updateDeviceData = $this->physicalDeviceHydrator->hydrateDevice($document->getResource(), $document->getIncluded(), $device);

			} else {
				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
					$this->translator->translate('messages.invalidType.heading'),
					$this->translator->translate('messages.invalidType.message'),
					[
						'pointer' => '/data/type',
					]
				);
			}

			$device = $this->devicesManager->update($device, $updateDeviceData);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (NodeWebServerExceptions\IJsonApiException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollback();

			throw $ex;

		} catch (Doctrine\DBAL\Exception\UniqueConstraintViolationException $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollback();

			if (preg_match("%key '(?P<key>.+)_unique'%", $ex->getMessage(), $match)) {
				if (Utils\Strings::startsWith($match['key'], 'device_')) {
					throw new NodeWebServerExceptions\JsonApiErrorException(
						StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
						$this->translator->translate('//node.base.messages.uniqueConstraint.heading'),
						$this->translator->translate('//node.base.messages.uniqueConstraint.message'),
						[
							'pointer' => '/data/attributes/' . Utils\Strings::substring($match['key'], 7),
						]
					);
				}
			}

			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('//node.base.messages.uniqueConstraint.heading'),
				$this->translator->translate('//node.base.messages.uniqueConstraint.message')
			);

		} catch (Throwable $ex) {
			// Revert all changes when error occur
			$this->getOrmConnection()->rollback();

			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.notUpdated.heading'),
				$this->translator->translate('messages.notUpdated.message')
			);
		}

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($device));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\JsonApiErrorException
	 * @throws Doctrine\DBAL\ConnectionException
	 */
	public function delete(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$device = $this->findDevice($request->getAttribute(Router\Router::URL_ITEM_ID));

		try {
			// Start transaction connection to the database
			$this->getOrmConnection()->beginTransaction();

			foreach ($device->getChannels() as $channel) {
				// Remove channels. Newly connected device will be reinitialized with all channels
				$this->channelsManager->delete($channel);
			}

			// Move device back into warehouse
			$this->devicesManager->delete($device);

			// Commit all changes into database
			$this->getOrmConnection()->commit();

		} catch (Throwable $ex) {
			// Log catched exception
			$this->logger->error('[CONTROLLER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			// Revert all changes when error occur
			$this->getOrmConnection()->rollback();

			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNPROCESSABLE_ENTITY,
				$this->translator->translate('messages.notDeleted.heading'),
				$this->translator->translate('messages.notDeleted.message')
			);
		}

		/** @var NodeWebServerHttp\Response $response */
		$response = $response->withStatus(StatusCodeInterface::STATUS_NO_CONTENT);

		return $response;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\JsonApiErrorException
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		$device = $this->findDevice($request->getAttribute(Router\Router::URL_ITEM_ID));

		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if ($relationEntity === Schemas\Devices\DeviceSchema::RELATIONSHIPS_PROPERTIES) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($device->getProperties()));

		} elseif ($relationEntity === Schemas\Devices\DeviceSchema::RELATIONSHIPS_CONFIGURATION) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($device->getConfiguration()));

		} elseif ($relationEntity === Schemas\Devices\DeviceSchema::RELATIONSHIPS_CHILDREN) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($device->getChildren()));

		} elseif ($relationEntity === Schemas\Devices\DeviceSchema::RELATIONSHIPS_CHANNELS) {
			$findQuery = new Queries\FindChannelsQuery();
			$findQuery->forDevice($device);

			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($this->channelRepository->findAllBy($findQuery)));

		} elseif (
			$relationEntity === Schemas\Devices\PhysicalDeviceSchema::RELATIONSHIPS_CREDENTIALS
			&& $device instanceof Entities\Devices\IPhysicalDevice
		) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($device->getCredentials()));

		} elseif (
			$relationEntity === Schemas\Devices\PhysicalDeviceSchema::RELATIONSHIPS_HARDWARE
			&& $device instanceof Entities\Devices\IPhysicalDevice
		) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($device->getHardware()));

		} elseif (
			$relationEntity === Schemas\Devices\PhysicalDeviceSchema::RELATIONSHIPS_FIRMWARE
			&& $device instanceof Entities\Devices\IPhysicalDevice
		) {
			return $response
				->withEntity(NodeWebServerHttp\ScalarEntity::from($device->getFirmware()));
		}

		$this->throwUnknownRelation($relationEntity);

		return $response;
	}

}
