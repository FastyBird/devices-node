<?php declare(strict_types = 1);

/**
 * ChannelHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 * @since          1.0.0
 *
 * @date           13.04.19
 */

namespace FastyBird\DevicesNode\Hydrators\Channels;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Hydrators;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use IPub\JsonAPIDocument;
use Nette\Utils;
use Throwable;

/**
 * Device channel entity hydrator
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelHydrator extends Hydrators\Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		'title',
		'comment',
	];

	/** @var string */
	protected $translationDomain = 'node.channels';

	/**
	 * {@inheritDoc}
	 */
	public function hydrate(
		JsonAPIDocument\Objects\IResourceObject $resource,
		$entity = null
	): Utils\ArrayHash {
		throw new Exceptions\InvalidStateException('This method is overridden. Use method `hydrateChannel` instead.');
	}

	/**
	 * @param JsonAPIDocument\Objects\IResourceObject<mixed> $resource
	 * @param Entities\Channels\IChannel|null $entity
	 *
	 * @return Utils\ArrayHash
	 *
	 * @throws NodeWebServerExceptions\JsonApiErrorException
	 * @throws NodeWebServerExceptions\JsonApiMultipleErrorException
	 * @throws Throwable
	 */
	public function hydrateChannel(
		JsonAPIDocument\Objects\IResourceObject $resource,
		?Entities\Channels\IChannel $entity = null
	): Utils\ArrayHash {
		return parent::hydrate($resource, $entity);
	}

	/**
	 * {@inheritDoc}
	 */
	protected function getEntityName(): string
	{
		return Entities\Channels\Channel::class;
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return string|null
	 */
	protected function hydrateTitleAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): ?string
	{
		if ($attributes->get('title') === null || (string) $attributes->get('title') === '') {
			return null;
		}

		return (string) $attributes->get('title');
	}

	/**
	 * @param JsonAPIDocument\Objects\IStandardObject<mixed> $attributes
	 *
	 * @return string|null
	 */
	protected function hydrateCommentAttribute(JsonAPIDocument\Objects\IStandardObject $attributes): ?string
	{
		if ($attributes->get('comment') === null || (string) $attributes->get('comment') === '') {
			return null;
		}

		return (string) $attributes->get('comment');
	}

}
