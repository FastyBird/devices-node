<?php declare(strict_types = 1);

/**
 * DevicePropertiesV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           04.06.19
 */

namespace FastyBird\DevicesNode\Controllers;

use FastyBird\DevicesNode\Controllers;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Queries;
use FastyBird\DevicesNode\Router;
use FastyBird\DevicesNode\Schemas;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message;
use Ramsey\Uuid;

/**
 * Device properties API controller
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class DevicePropertiesV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TDeviceFinder;

	/** @var Models\Devices\IDeviceRepository */
	protected $deviceRepository;

	/** @var Models\Devices\Properties\IPropertyRepository */
	private $propertyRepository;

	/** @var string */
	protected $translationDomain = 'node.deviceProperties';

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Devices\Properties\IPropertyRepository $propertyRepository
	) {
		$this->deviceRepository = $deviceRepository;
		$this->propertyRepository = $propertyRepository;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	public function index(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// At first, try to load device
		$device = $this->findDevice($request->getAttribute(Router\Router::URL_DEVICE_ID));

		$findQuery = new Queries\FindDevicePropertiesQuery();
		$findQuery->forDevice($device);

		$properties = $this->propertyRepository->getResultSet($findQuery);

		return $response
			->withEntity(NodeWebServerHttp\ScalarEntity::from($properties));
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	public function read(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// At first, try to load device
		$device = $this->findDevice($request->getAttribute(Router\Router::URL_DEVICE_ID));

		if (Uuid\Uuid::isValid($request->getAttribute(Router\Router::URL_ITEM_ID))) {
			$findQuery = new Queries\FindDevicePropertiesQuery();
			$findQuery->forDevice($device);
			$findQuery->byId(Uuid\Uuid::fromString($request->getAttribute(Router\Router::URL_ITEM_ID)));

			// & property
			$property = $this->propertyRepository->findOneBy($findQuery);

			if ($property !== null) {
				return $response
					->withEntity(NodeWebServerHttp\ScalarEntity::from($property));
			}
		}

		throw new NodeJsonApiExceptions\JsonApiErrorException(
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
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	public function readRelationship(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		// At first, try to load device
		$device = $this->findDevice($request->getAttribute(Router\Router::URL_DEVICE_ID));

		// & relation entity name
		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if (Uuid\Uuid::isValid($request->getAttribute(Router\Router::URL_ITEM_ID))) {
			$findQuery = new Queries\FindDevicePropertiesQuery();
			$findQuery->forDevice($device);
			$findQuery->byId(Uuid\Uuid::fromString($request->getAttribute(Router\Router::URL_ITEM_ID)));

			// & property
			$property = $this->propertyRepository->findOneBy($findQuery);

			if ($property !== null) {
				if ($relationEntity === Schemas\Devices\Properties\PropertySchema::RELATIONSHIPS_DEVICE) {
					return $response
						->withEntity(NodeWebServerHttp\ScalarEntity::from($device));
				}

			} else {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
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
