<?php declare(strict_types = 1);

/**
 * TChannelFinder.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           13.04.19
 */

namespace FastyBird\DevicesNode\Controllers\Finders;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Queries;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Localization;
use Ramsey\Uuid;

/**
 * @property-read Localization\ITranslator $translator
 * @property-read Models\Channels\IChannelRepository $channelRepository
 */
trait TChannelFinder
{

	/**
	 * @param string $id
	 * @param Entities\Devices\IDevice $device
	 *
	 * @return Entities\Channels\IChannel
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	protected function findChannel(
		string $id,
		Entities\Devices\IDevice $device
	): Entities\Channels\IChannel {
		try {
			$findQuery = new Queries\FindChannelsQuery();
			$findQuery->byId(Uuid\Uuid::fromString($id));
			$findQuery->forDevice($device);

			$channel = $this->channelRepository->findOneBy($findQuery);

			if ($channel === null) {
				throw new NodeJsonApiExceptions\JsonApiErrorException(
					StatusCodeInterface::STATUS_NOT_FOUND,
					$this->translator->translate('//node.base.messages.notFound.heading'),
					$this->translator->translate('//node.base.messages.notFound.message')
				);
			}

		} catch (Uuid\Exception\InvalidUuidStringException $ex) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.notFound.heading'),
				$this->translator->translate('//node.base.messages.notFound.message')
			);
		}

		return $channel;
	}

}
