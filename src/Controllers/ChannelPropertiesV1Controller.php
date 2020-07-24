<?php declare(strict_types = 1);

/**
 * ChannelPropertiesV1Controller.php
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
 * Device channel properties API controller
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelPropertiesV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TDeviceFinder;
	use Controllers\Finders\TChannelFinder;

	/** @var Models\Devices\IDeviceRepository */
	protected $deviceRepository;

	/** @var Models\Channels\IChannelRepository */
	protected $channelRepository;

	/** @var Models\Channels\Properties\IPropertyRepository */
	protected $propertyRepository;

	/** @var string */
	protected $translationDomain = 'node.channelProperties';

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Channels\IChannelRepository $channelRepository,
		Models\Channels\Properties\IPropertyRepository $propertyRepository
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;
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

		// & channel
		$channel = $this->findChannel($request->getAttribute(Router\Router::URL_CHANNEL_ID), $device);

		$findQuery = new Queries\FindChannelPropertiesQuery();
		$findQuery->forChannel($channel);

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

		// & channel
		$channel = $this->findChannel($request->getAttribute(Router\Router::URL_CHANNEL_ID), $device);

		if (Uuid\Uuid::isValid($request->getAttribute(Router\Router::URL_ITEM_ID))) {
			$findQuery = new Queries\FindChannelPropertiesQuery();
			$findQuery->forChannel($channel);
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

		// & channel
		$channel = $this->findChannel($request->getAttribute(Router\Router::URL_CHANNEL_ID), $device);

		// & relation entity name
		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if (Uuid\Uuid::isValid($request->getAttribute(Router\Router::URL_ITEM_ID))) {
			$findQuery = new Queries\FindChannelPropertiesQuery();
			$findQuery->forChannel($channel);
			$findQuery->byId(Uuid\Uuid::fromString($request->getAttribute(Router\Router::URL_ITEM_ID)));

			// & property
			$property = $this->propertyRepository->findOneBy($findQuery);

			if ($property !== null) {
				if ($relationEntity === Schemas\Channels\Properties\PropertySchema::RELATIONSHIPS_CHANNEL) {
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
