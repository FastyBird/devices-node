<?php declare(strict_types = 1);

/**
 * ChannelConfigurationV1Controller.php
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

use FastyBird\DevicesNode;
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
 * Device channel configuration API controller
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @Secured
 * @Secured\User(loggedIn)
 */
final class ChannelConfigurationV1Controller extends BaseV1Controller
{

	use Controllers\Finders\TDeviceFinder;
	use Controllers\Finders\TChannelFinder;

	/** @var Models\Devices\IDeviceRepository */
	protected $deviceRepository;

	/** @var Models\Channels\IChannelRepository */
	protected $channelRepository;

	/** @var Models\Channels\Configuration\IRowRepository */
	private $rowRepository;

	/** @var string */
	protected $translationDomain = 'node.channelConfiguration';

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Channels\IChannelRepository $channelRepository,
		Models\Channels\Configuration\IRowRepository $rowRepository
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;
		$this->rowRepository = $rowRepository;
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

		if (!$channel->hasControl(DevicesNode\Constants::CONTROL_CONFIG)) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.notFound.heading'),
				$this->translator->translate('//node.base.messages.notFound.message')
			);
		}

		$findQuery = new Queries\FindChannelConfigurationQuery();
		$findQuery->forChannel($channel);

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

		if (!$channel->hasControl(DevicesNode\Constants::CONTROL_CONFIG)) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.notFound.heading'),
				$this->translator->translate('//node.base.messages.notFound.message')
			);
		}

		if (Uuid\Uuid::isValid($request->getAttribute(Router\Router::URL_ITEM_ID))) {
			$findQuery = new Queries\FindChannelConfigurationQuery();
			$findQuery->forChannel($channel);
			$findQuery->byId(Uuid\Uuid::fromString($request->getAttribute(Router\Router::URL_ITEM_ID)));

			// & configuration row
			$row = $this->rowRepository->findOneBy($findQuery);

			if ($row !== null) {
				return $response
					->withEntity(NodeWebServerHttp\ScalarEntity::from($row));
			}
		}

		throw new NodeJsonApiExceptions\JsonApiErrorException(
			StatusCodeInterface::STATUS_NOT_FOUND,
			$this->translator->translate('//node.base.messages.notFound.heading'),
			$this->translator->translate('//node.base.messages.notFound.message')
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

		if (!$channel->hasControl(DevicesNode\Constants::CONTROL_CONFIG)) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.notFound.heading'),
				$this->translator->translate('//node.base.messages.notFound.message')
			);
		}

		// & relation entity name
		$relationEntity = strtolower($request->getAttribute(Router\Router::RELATION_ENTITY));

		if (Uuid\Uuid::isValid($request->getAttribute(Router\Router::URL_ITEM_ID))) {
			$findQuery = new Queries\FindChannelConfigurationQuery();
			$findQuery->forChannel($channel);
			$findQuery->byId(Uuid\Uuid::fromString($request->getAttribute(Router\Router::URL_ITEM_ID)));

			// & configuration row
			$row = $this->rowRepository->findOneBy($findQuery);

			if ($row !== null) {
				if ($relationEntity === Schemas\Channels\Configuration\RowSchema::RELATIONSHIPS_CHANNEL) {
					return $response
						->withEntity(NodeWebServerHttp\ScalarEntity::from($device));
				}

			} else {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_NOT_FOUND,
					$this->translator->translate('//node.base.messages.notFound.heading'),
					$this->translator->translate('//node.base.messages.notFound.message')
				);
			}
		}

		return parent::readRelationship($request, $response);
	}

}
