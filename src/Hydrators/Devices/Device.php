<?php declare(strict_types = 1);

/**
 * Device.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 * @since          1.0.0
 *
 * @date           07.06.19
 */

namespace FastyBird\DevicesNode\Hydrators\Devices;

use FastyBird\DevicesNode\Hydrators;
use IPub\JsonAPIDocument;

/**
 * Device entity hydrator
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Hydrators
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class Device extends Hydrators\Hydrator
{

	/** @var string */
	protected $entityIdentifier = self::IDENTIFIER_KEY;

	/** @var string[] */
	protected $attributes = [
		'title',
		'comment',
		'enabled',
	];

	/**
	 * @param JsonAPIDocument\Objects\IResourceObject<mixed> $attributes
	 *
	 * @return string|null
	 */
	protected function hydrateTitleAttribute(JsonAPIDocument\Objects\IResourceObject $attributes): ?string
	{
		if ($attributes->get('title') === null || (string) $attributes->get('title') === '') {
			return null;
		}

		return (string) $attributes->get('title');
	}

	/**
	 * @param JsonAPIDocument\Objects\IResourceObject<mixed> $attributes
	 *
	 * @return string|null
	 */
	protected function hydrateCommentAttribute(JsonAPIDocument\Objects\IResourceObject $attributes): ?string
	{
		if ($attributes->get('comment') === null || (string) $attributes->get('comment') === '') {
			return null;
		}

		return (string) $attributes->get('comment');
	}

	/**
	 * @param JsonAPIDocument\Objects\IResourceObject<mixed> $attributes
	 *
	 * @return bool
	 */
	protected function hydrateEnabledAttribute(JsonAPIDocument\Objects\IResourceObject $attributes): bool
	{
		return (bool) $attributes->get('enabled');
	}

}
