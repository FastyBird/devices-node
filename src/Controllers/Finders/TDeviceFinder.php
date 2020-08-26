<?php declare(strict_types = 1);

/**
 * TDeviceFinder.php
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
 * @property-read Models\Devices\IDeviceRepository $deviceRepository
 */
trait TDeviceFinder
{

	/**
	 * @param string $id
	 *
	 * @return Entities\Devices\IDevice
	 *
	 * @throws NodeJsonApiExceptions\IJsonApiException
	 */
	protected function findDevice(string $id): Entities\Devices\IDevice
	{
		try {
			$findQuery = new Queries\FindDevicesQuery();
			$findQuery->byId(Uuid\Uuid::fromString($id));

			$device = $this->deviceRepository->findOneBy($findQuery);

			if ($device === null) {
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

		return $device;
	}

}
