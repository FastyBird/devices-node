<?php declare(strict_types = 1);

/**
 * DeviceSchema.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Schemas
 * @since          0.1.0
 *
 * @date           13.04.19
 */

namespace FastyBird\DevicesNode\Schemas\Devices;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Queries;
use FastyBird\DevicesNode\Router;
use FastyBird\DevicesNode\Schemas;
use FastyBird\NodeJsonApi\Schemas as NodeJsonApiSchemas;
use IPub\SlimRouter\Routing;
use Neomerx\JsonApi;

/**
 * Device entity schema
 *
 * @package          FastyBird:DevicesNode!
 * @subpackage       Schemas
 *
 * @author           Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template T of Entities\Devices\IDevice
 * @phpstan-extends  NodeJsonApiSchemas\JsonApiSchema<T>
 */
abstract class DeviceSchema extends NodeJsonApiSchemas\JsonApiSchema
{

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_CHANNELS = 'channels';

	public const RELATIONSHIPS_PROPERTIES = 'properties';
	public const RELATIONSHIPS_CONFIGURATION = 'configuration';

	public const RELATIONSHIPS_PARENT = 'parent';
	public const RELATIONSHIPS_CHILDREN = 'children';

	/** @var Models\Devices\IDeviceRepository */
	protected $deviceRepository;

	/** @var Models\Channels\IChannelRepository */
	protected $channelRepository;

	/** @var Routing\IRouter */
	protected $router;

	public function __construct(
		Models\Devices\IDeviceRepository $deviceRepository,
		Models\Channels\IChannelRepository $channelRepository,
		Routing\IRouter $router
	) {
		$this->deviceRepository = $deviceRepository;
		$this->channelRepository = $channelRepository;

		$this->router = $router;
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, string>
	 *
	 * @phpstan-param T $device
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($device, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			'identifier' => $device->getIdentifier(),
			'name'       => $device->getName(),
			'title'      => $device->getTitle(),
			'comment'    => $device->getComment(),

			'state'      => $device->getState()->getValue(),
			'is_enabled' => $device->isEnabled(),

			'control' => $this->formatControls($device->getControls()),

			'owner' => $device->getOwnerId(),
		];
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param T $device
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getSelfLink($device): JsonApi\Contracts\Schema\LinkInterface
	{
		return new JsonApi\Schema\Link(
			false,
			$this->router->urlFor(
				'device',
				[
					Router\Router::URL_ITEM_ID => $device->getPlainId(),
				]
			),
			false
		);
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpstan-param T $device
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($device, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		$relationships = [
			self::RELATIONSHIPS_PROPERTIES    => [
				self::RELATIONSHIP_DATA          => $device->getProperties(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_CONFIGURATION => [
				self::RELATIONSHIP_DATA          => $device->getConfiguration(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_CHANNELS      => [
				self::RELATIONSHIP_DATA          => $this->getChannels($device),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_CHILDREN      => [
				self::RELATIONSHIP_DATA          => $this->getChildren($device),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
		];

		if ($device->getParent() !== null) {
			$relationships[self::RELATIONSHIPS_PARENT] = [
				self::RELATIONSHIP_DATA          => $device->getParent(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			];
		}

		return $relationships;
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param T $device
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($device, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_PROPERTIES) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'device.properties',
					[
						Router\Router::URL_DEVICE_ID => $device->getPlainId(),
					]
				),
				true,
				[
					'count' => count($device->getProperties()),
				]
			);

		} elseif ($name === self::RELATIONSHIPS_CONFIGURATION) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'device.configuration.rows',
					[
						Router\Router::URL_DEVICE_ID => $device->getPlainId(),
					]
				),
				true,
				[
					'count' => count($device->getConfiguration()),
				]
			);

		} elseif ($name === self::RELATIONSHIPS_CHANNELS) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'channels',
					[
						Router\Router::URL_DEVICE_ID => $device->getPlainId(),
					]
				),
				true,
				[
					'count' => count($device->getChannels()),
				]
			);

		} elseif ($name === self::RELATIONSHIPS_PARENT && $device->getParent() !== null) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'device',
					[
						Router\Router::URL_ITEM_ID => $device->getPlainId(),
					]
				),
				false
			);

		} elseif ($name === self::RELATIONSHIPS_CHILDREN) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'device.children',
					[
						Router\Router::URL_DEVICE_ID => $device->getPlainId(),
					]
				),
				true,
				[
					'count' => count($device->getChildren()),
				]
			);
		}

		return parent::getRelationshipRelatedLink($device, $name);
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param T $device
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipSelfLink($device, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if (
			$name === self::RELATIONSHIPS_PROPERTIES
			|| $name === self::RELATIONSHIPS_CONFIGURATION
			|| $name === self::RELATIONSHIPS_CHANNELS
			|| $name === self::RELATIONSHIPS_CHILDREN
			|| ($name === self::RELATIONSHIPS_PARENT && $device->getParent() !== null)
		) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'device.relationship',
					[
						Router\Router::URL_ITEM_ID     => $device->getPlainId(),
						Router\Router::RELATION_ENTITY => $name,

					]
				),
				false
			);
		}

		return parent::getRelationshipSelfLink($device, $name);
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 *
	 * @return Entities\Channels\IChannel[]
	 */
	private function getChannels(Entities\Devices\IDevice $device): array
	{
		$findQuery = new Queries\FindChannelsQuery();
		$findQuery->forDevice($device);

		return $this->channelRepository->findAllBy($findQuery);
	}

	/**
	 * @param Entities\Devices\IDevice $device
	 *
	 * @return Entities\Devices\IDevice[]
	 */
	private function getChildren(Entities\Devices\IDevice $device): array
	{
		$findQuery = new Queries\FindDevicesQuery();
		$findQuery->forParent($device);

		return $this->deviceRepository->findAllBy($findQuery);
	}

	/**
	 * @param Entities\Devices\Controls\IControl[] $controls
	 *
	 * @return string[]
	 */
	private function formatControls(array $controls): array
	{
		$return = [];

		foreach ($controls as $control) {
			$return[] = $control->getName();
		}

		return $return;
	}

}
