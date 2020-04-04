<?php declare(strict_types = 1);

/**
 * DeviceConfigurationV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           04.06.19
 */

namespace FastyBird\DevicesNode\Controllers;

use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Controllers;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Queries;
use FastyBird\DevicesNode\Router;
use FastyBird\DevicesNode\Schemas;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message;
use Ramsey\Uuid;

/**
 * Device configuration API controller
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DeviceConfigurationV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TDeviceFinder;

	/** @var Models\Devices\IDeviceRepository */
	protected $deviceRepository;

	/** @var Models\Devices\Configuration\IRowRepository */
	private $rowRepository;

	/** @var string */
	protected $translationDomain = 'node.deviceConfiguration';

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Devices\Configuration\IRowRepository $rowRepository
	) {
		$this->deviceRepository = $deviceRepository;
		$this->rowRepository = $rowRepository;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	public function index(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// At first, try to load device
		$device = $this->findDevice($request->getAttribute(Router\Router::URL_DEVICE_ID));

		if (!$device->hasControl(DevicesNode\Constants::CONTROL_CONFIG)) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.invalidDeviceType.heading'),
				$this->translator->translate('//node.base.messages.invalidDeviceType.message')
			);
		}

		$findQuery = new Queries\FindDeviceConfigurationQuery();
		$findQuery->forDevice($device);

		$rows = $this->rowRepository->getResultSet($findQuery);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($rows));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	public function read(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// At first, try to load device
		$device = $this->findDevice($request->getAttribute(Router\Router::URL_DEVICE_ID));

		if (!$device->hasControl(DevicesNode\Constants::CONTROL_CONFIG)) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.invalidDeviceType.heading'),
				$this->translator->translate('//node.base.messages.invalidDeviceType.message')
			);
		}

		if (Uuid\Uuid::isValid($request->getAttribute(Router\Router::URL_ITEM_ID))) {
			$findQuery = new Queries\FindDeviceConfigurationQuery();
			$findQuery->forDevice($device);
			$findQuery->byId(Uuid\Uuid::fromString($request->getAttribute(Router\Router::URL_ITEM_ID)));

			// & configuration row
			$row = $this->rowRepository->findOneBy($findQuery);

			if ($row !== null) {
				return $response
					->withEntity(NodeWebServerHttp\ScalarEntity::from($row));
			}
		}

		throw new NodeWebServerExceptions\JsonApiErrorException(
			StatusCodeInterface::STATUS_NOT_FOUND,
			$this->translator->translate('messages.notFound.heading'),
			$this->translator->translate('messages.notFound.message')
		);
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// At first, try to load device
		$device = $this->findDevice($request->getAttribute(Router\Router::URL_DEVICE_ID));

		if (!$device->hasControl(DevicesNode\Constants::CONTROL_CONFIG)) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.invalidDeviceType.heading'),
				$this->translator->translate('//node.base.messages.invalidDeviceType.message')
			);
		}

		// & relation entity name
		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if (Uuid\Uuid::isValid($request->getAttribute(Router\Router::URL_ITEM_ID))) {
			$findQuery = new Queries\FindDeviceConfigurationQuery();
			$findQuery->forDevice($device);
			$findQuery->byId(Uuid\Uuid::fromString($request->getAttribute(Router\Router::URL_ITEM_ID)));

			// & configuration row
			$row = $this->rowRepository->findOneBy($findQuery);

			if ($row !== null) {
				if ($relationEntity === Schemas\Devices\Configuration\RowSchema::RELATIONSHIPS_DEVICE) {
					return $response
						->withEntity(NodeWebServerHttp\ScalarEntity::from($device));
				}

			} else {
				throw new NodeWebServerExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_NOT_FOUND,
					$this->translator->translate('messages.notFound.heading'),
					$this->translator->translate('messages.notFound.message')
				);
			}
		}

		$this->throwUnknownRelation($relationEntity);

		return $response;
	}

}
