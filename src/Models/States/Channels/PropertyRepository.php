<?php declare(strict_types = 1);

/**
 * PropertyRepository.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           02.03.20
 */

namespace FastyBird\DevicesNode\Models\States\Channels;

use FastyBird\DevicesNode\Connections;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Queries;
use FastyBird\DevicesNode\States;
use Nette;
use PHPOnCouch;
use Psr\Log;
use Ramsey\Uuid;
use stdClass;
use Throwable;

/**
 * Channel property repository
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class PropertyRepository implements IPropertyRepository
{

	use Nette\SmartObject;

	/** @var Models\Channels\Properties\IPropertyRepository */
	private $propertyRepository;

	/** @var Connections\ICouchDbConnection */
	private $dbClient;

	/** @var Log\LoggerInterface */
	private $logger;

	public function __construct(
		Models\Channels\Properties\IPropertyRepository $propertyRepository,
		Connections\ICouchDbConnection $dbClient,
		Log\LoggerInterface $logger
	) {
		$this->propertyRepository = $propertyRepository;

		$this->dbClient = $dbClient;

		$this->logger = $logger;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findOne(
		Uuid\UuidInterface $id
	): ?States\Channels\IProperty {
		$doc = $this->getDocument($id);

		if ($doc === null) {
			return null;
		}

		$findProperty = new Queries\FindChannelPropertiesQuery();
		$findProperty->byId($id);

		$property = $this->propertyRepository->findOneBy($findProperty);

		if ($property === null) {
			return null;
		}

		/** @var States\Channels\IProperty $state */
		$state = States\StateFactory::create(States\Channels\Property::class, $doc, $property);

		return $state;
	}

	/**
	 * {@inheritDoc}
	 */
	public function findValue(
		Uuid\UuidInterface $id
	) {
		$state = $this->findOne($id);

		if ($state === null) {
			return null;
		}

		return $state->getValue();
	}

	/**
	 * {@inheritDoc}
	 */
	public function findExpected(
		Uuid\UuidInterface $id
	) {
		$state = $this->findOne($id);

		if ($state === null) {
			return null;
		}

		return $state->getExpected();
	}

	/**
	 * @param Uuid\UuidInterface $id
	 *
	 * @return PHPOnCouch\CouchDocument|null
	 */
	private function getDocument(
		Uuid\UuidInterface $id
	): ?PHPOnCouch\CouchDocument {
		try {
			$this->dbClient->getClient()->asCouchDocuments();

			/** @var stdClass[]|mixed $docs */
			$docs = $this->dbClient->getClient()->find([
				'id'   => [
					'$eq' => $id->toString(),
				],
			]);

			if (is_array($docs) && count($docs) >= 1) {
				$doc = new PHPOnCouch\CouchDocument($this->dbClient->getClient());

				return $doc->loadFromObject($docs[0]);
			}

			return null;

		} catch (PHPOnCouch\Exceptions\CouchNotFoundException $ex) {
			return null;

		} catch (Throwable $ex) {
			$this->logger->error('[MODEL] Document could not be loaded', [
				'type'      => 'repository',
				'action'    => 'find_document',
				'property'  => $id->toString(),
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			throw new Exceptions\InvalidStateException('Document could not be loaded from database', 0, $ex);
		}
	}

}
