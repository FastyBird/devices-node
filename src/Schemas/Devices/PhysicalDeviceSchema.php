<?php declare(strict_types = 1);

/**
 * PhysicalDeviceSchema.php
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
use FastyBird\DevicesNode\Router;
use Neomerx\JsonApi;

/**
 * Machine device entity schema
 *
 * @package         FastyBird:DevicesNode!
 * @subpackage      Schemas
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @phpstan-extends DeviceSchema<Entities\Devices\IPhysicalDevice>
 */
final class PhysicalDeviceSchema extends DeviceSchema
{

	/**
	 * Define entity schema type string
	 */
	public const SCHEMA_TYPE = 'devices-node/physical-device';

	/**
	 * Define relationships names
	 */
	public const RELATIONSHIPS_HARDWARE = 'hardware';
	public const RELATIONSHIPS_FIRMWARE = 'firmware';

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return self::SCHEMA_TYPE;
	}

	/**
	 * {@inheritDoc}
	 */
	public function getEntityClass(): string
	{
		return Entities\Devices\PhysicalDevice::class;
	}

	/**
	 * @param Entities\Devices\IPhysicalDevice $device
	 * @param JsonApi\Contracts\Schema\ContextInterface $context
	 *
	 * @return iterable<string, mixed>
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationships($device, JsonApi\Contracts\Schema\ContextInterface $context): iterable
	{
		return array_merge([
			self::RELATIONSHIPS_HARDWARE => [
				self::RELATIONSHIP_DATA          => $device->getHardware(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
			self::RELATIONSHIPS_FIRMWARE => [
				self::RELATIONSHIP_DATA          => $device->getFirmware(),
				self::RELATIONSHIP_LINKS_SELF    => true,
				self::RELATIONSHIP_LINKS_RELATED => true,
			],
		], (array) parent::getRelationships($device, $context));
	}

	/**
	 * @param Entities\Devices\IPhysicalDevice $device
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipRelatedLink($device, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if ($name === self::RELATIONSHIPS_HARDWARE) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'device.hardware',
					[
						Router\Router::URL_DEVICE_ID => $device->getPlainId(),
					]
				),
				false
			);

		} elseif ($name === self::RELATIONSHIPS_FIRMWARE) {
			return new JsonApi\Schema\Link(
				false,
				$this->router->urlFor(
					'device.firmware',
					[
						Router\Router::URL_DEVICE_ID => $device->getPlainId(),
					]
				),
				false
			);
		}

		return parent::getRelationshipRelatedLink($device, $name);
	}

	/**
	 * @param Entities\Devices\IPhysicalDevice $device
	 * @param string $name
	 *
	 * @return JsonApi\Contracts\Schema\LinkInterface
	 *
	 * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
	 */
	public function getRelationshipSelfLink($device, string $name): JsonApi\Contracts\Schema\LinkInterface
	{
		if (
			$name === self::RELATIONSHIPS_HARDWARE
			|| $name === self::RELATIONSHIPS_FIRMWARE
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

}
