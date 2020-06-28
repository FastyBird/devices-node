<?php declare(strict_types = 1);

/**
 * ChannelHydrator.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 * @since          0.1.0
 *
 * @date           13.04.19
 */

namespace FastyBird\DevicesNode\Hydrators\Channels;

use FastyBird\DevicesNode\Entities;
use FastyBird\NodeJsonApi\Hydrators as NodeJsonApiHydrators;
use IPub\JsonAPIDocument;

/**
 * Device channel entity hydrator
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class ChannelHydrator extends NodeJsonApiHydrators\Hydrator
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
