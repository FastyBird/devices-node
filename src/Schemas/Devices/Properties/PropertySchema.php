<?php declare(strict_types = 1);

/**
 * PropertySchema.php
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

namespace FastyBird\DevicesNode\Schemas\Devices\Properties;

use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Router;
use FastyBird\DevicesNode\Schemas;
use FastyBird\NodeJsonApi\Schemas as NodeJsonApiSchemas;
use IPub\SlimRouter\Routing;
use Neomerx\JsonApi;

/**
 * Device property entity schema
 *
 * @package         FastyBird:DevicesNode!
 * @subpackage      Schemas
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends NodeJsonApiSchemas\JsonApiSchema<Entities\Devices\Properties\IProperty>
 */
final class PropertySchema extends NodeJsonApiSchemas\JsonApiSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'devices-node/device-property';

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_DEVICE = 'device';

	/** @var Routing\IRouter */
	private $router;

	/** @var Models\States\Devices\IPropertyRepository */
	private $propertyRepository;

	public function __construct(
		Routing\IRouter $router,
		Models\States\Devices\IPropertyRepository $propertyRepository
	) {
		$this->router = $router;
		$this->propertyRepository = $propertyRepository;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Devices\Properties\Property::class;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * @param Entities\Devices\Properties\IProperty $property
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, string|bool|null>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getAttributes($property, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		$state = $this->propertyRepository->findOne($property->getId());

		return [
			'property'     => $property->getProperty(),
			'name'         => $property->getName(),
			'is_settable'  => $property->isSettable(),
			'is_queryable' => $property->isQueryable(),
			'datatype'     => $property->getDatatype() !== null ? $property->getDatatype()->getValue() : null,
			'unit'         => $property->getUnit(),
			'format'       => is_array($property->getFormat()) ? implode(',', $property->getFormat()) : $property->getFormat(),
			'value'        => $state !== null ? $state->getValue() : null,
			'expected'     => $state !== null ? $state->getExpected() : null,
			'pending'      => $state !== null ? $state->isPending() : false,
		];
	}

	/**
	 * @param Entities\Devices\Properties\IProperty $property
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getSelfLink($property): JsonApi\Contracts\Schema\LinkInterface
	{
		return new JsonApi\Schema\Link(
			false,
			$this->router->urlFor(
				DevicesNode\Constants::ROUTE_NAME_DEVICE_PROPERTY,
				[
					Router\Router::URL_DEVICE_ID => $property->getDevice()->getPlainId(),
					Router\Router::URL_ITEM_ID   => $property->getPlainId(),
				]
			),
			false
		);
	}

	/**
	 * @param Entities\Devices\Properties\IProperty $property
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($property, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [
			self::RELATIONSHIPS_DEVICE => [
				self::RELATIONSHIP_DATA          => $property,
				self::RELATIONSHIP_LINKS_SELF    => false,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
		];
	}

	/**
	 * @param Entities\Devices\Properties\IProperty $property
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($property, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_DEVICE) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					DevicesNode\Constants::ROUTE_NAME_DEVICE,
					[
						Router\Router::URL_ITEM_ID => $property->getDevice()->getPlainId(),
					]
				),
				false
			);
		}

		return parent::getRelationshipRelatedLink($property, $name);
	}

}
