<?php declare(strict_types = 1);

/**
 * FirmwareSchema.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Schemas
 * @since          0.1.0
 *
 * @date           22.04.19
 */

namespace FastyBird\DevicesNode\Schemas\Devices\Firmware;

use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Router;
use FastyBird\DevicesNode\Schemas;
use FastyBird\NodeJsonApi\Schemas as NodeJsonApiSchemas;
use IPub\SlimRouter\Routing;
use Neomerx\JsonApi;

/**
 * Device firmware entity schema
 *
 * @package         FastyBird:DevicesNode!
 * @subpackage      Schemas
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends NodeJsonApiSchemas\JsonApiSchema<Entities\Devices\PhysicalDevice\IFirmware>
 */
final class FirmwareSchema extends NodeJsonApiSchemas\JsonApiSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'devices-node/firmware';

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_DEVICE = 'device';

	/** @var Routing\IRouter */
	private $router;

	public function __construct(Routing\IRouter $router)
	{
		$this->router = $router;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Devices\PhysicalDevice\Firmware::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * @param Entities\Devices\PhysicalDevice\IFirmware $firmware
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, string|null>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($firmware, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			'name'         => $firmware->getName(),
			'manufacturer' => $firmware->getManufacturer()->getValue(),
			'version'      => $firmware->getVersion(),
		];
	}

	/**
	 * @param Entities\Devices\PhysicalDevice\IFirmware $firmware
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getSelfLink($firmware): JsonApi\Contracts\Schema\LinkInterface
	{
		return new JsonApi\Schema\Link(
			false,
			$this->router->urlFor(
				DevicesNode\Constants::ROUTE_NAME_DEVICE_FIRMWARE,
				[
					Router\Router::URL_DEVICE_ID => $firmware->getDevice()->getPlainId(),
				]
			),
			false
		);
	}

	/**
	 * @param Entities\Devices\PhysicalDevice\IFirmware $firmware
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($firmware, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			self::RELATIONSHIPS_DEVICE => [
				self::RELATIONSHIP_DATA          => $firmware->getDevice(),
				self::RELATIONSHIP_LINKS_SELF    => false,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
		];
	}

	/**
	 * @param Entities\Devices\PhysicalDevice\IFirmware $firmware
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($firmware, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_DEVICE) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					DevicesNode\Constants::ROUTE_NAME_DEVICE,
					[
						Router\Router::URL_ITEM_ID => $firmware->getDevice()->getPlainId(),
					]
				),
				false
			);
		}

		return parent::getRelationshipRelatedLink($firmware, $name);
	}

}
