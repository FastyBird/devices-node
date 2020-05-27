<?php declare(strict_types = 1);

/**
 * JsonApiSchema.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Schemas
 * @since          0.1.0
 *
 * @date           01.06.19
 */

namespace FastyBird\DevicesNode\Schemas;

use FastyBird\DevicesNode\Exceptions;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use FastyBird\NodeWebServer\JsonApi as NodeWebServerJsonApi;
use Neomerx\JsonApi;
use Nette;

/**
 * Entity schema constructor
 *
 * @package            FastyBird:DevicesNode!
 * @subpackage         Schemas
 *
 * @author             Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-template   T of NodeDatabaseEntities\IEntity
 * @phpstan-implements NodeWebServerJsonApi\ISchema<T>
 */
abstract class JsonApiSchema implements NodeWebServerJsonApi\ISchema
{

	use Nette\SmartObject;

	/** @var string|null */
	private $subUrl = null;

	/**
	 * @param NodeDatabaseEntities\IEntity $resource
	 *
	 * @return string|null
	 *
	 * @phpstan-param T $resource
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getId($resource): ?string
	{
		return $resource->getPlainId();
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $resource
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpstan-param T $resource
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($resource, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return [];
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $resource
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param T $resource
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getSelfLink($resource): JsonApi\Contracts\Schema\LinkInterface
	{
		return new JsonApi\Schema\Link(true, $this->getSelfSubUrl($resource), false);
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $resource
	 *
	 * @return iterable<string, JsonApi\Contracts\Schema\LinkInterface>
	 *
	 * @phpstan-param T $resource
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getLinks($resource): iterable
	{
		return [
			JsonApi\Contracts\Schema\LinkInterface::SELF => $this->getSelfLink($resource),
		];
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $resource
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param T $resource
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipSelfLink($resource, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		// Feel free to override this method to change default URL or add meta
		$url = $this->getSelfSubUrl($resource) . '/' . JsonApi\Contracts\Schema\DocumentInterface::KEYWORD_RELATIONSHIPS . '/' . $name;

		return new JsonApi\Schema\Link(true, $url, false);
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $resource
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpstan-param T $resource
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($resource, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		// Feel free to override this method to change default URL or add meta
		$url = $this->getSelfSubUrl($resource) . '/' . $name;

		return new JsonApi\Schema\Link(true, $url, false);
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $resource
	 *
	 * @return bool
	 *
	 * @phpstan-param T $resource
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function hasIdentifierMeta($resource): bool
	{
		return false;
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $resource
	 *
	 * @return mixed
	 *
	 * @phpstan-param T $resource
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getIdentifierMeta($resource)
	{
		throw new Exceptions\LogicException('Default schema does not provide any meta');
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $resource
	 *
	 * @return bool
	 *
	 * @phpstan-param T $resource
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function hasResourceMeta($resource): bool
	{
		return false;
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $resource
	 *
	 * @return mixed
	 *
	 * @phpstan-param T $resource
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getResourceMeta($resource)
	{
		throw new Exceptions\LogicException('Default schema does not provide any meta');
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAddSelfLinkInRelationshipByDefault(string $relationshipName): bool
	{
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function isAddRelatedLinkInRelationshipByDefault(string $relationshipName): bool
	{
		return true;
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $resource
	 *
	 * @return string
	 *
	 * @phpstan-param T $resource
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	private function getSelfSubUrl($resource): string
	{
		return $this->getResourcesSubUrl() . '/' . $this->getId($resource);
	}

	/**
	 * Get resources sub-URL.
	 *
	 * @return string
	 */
	private function getResourcesSubUrl(): string
	{
		if ($this->subUrl === null) {
			$this->subUrl = '/' . $this->getType();
		}

		return $this->subUrl;
	}

}
