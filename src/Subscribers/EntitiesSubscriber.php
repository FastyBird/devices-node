<?php declare(strict_types = 1);

/**
 * EntitiesSubscriber.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Subscribers
 * @since          1.0.0
 *
 * @date           22.03.20
 */

namespace FastyBird\DevicesNode\Subscribers;

use Consistence;
use Doctrine\Common;
use Doctrine\ORM;
use Doctrine\Persistence;
use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\DevicesNode\Models;
use FastyBird\NodeDatabase\Entities as NodeDatabaseEntities;
use FastyBird\NodeExchange\Publishers as NodeExchangePublishers;
use Nette;
use Ramsey\Uuid;
use ReflectionClass;
use ReflectionException;

/**
 * Doctrine entities events
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Subscribers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class EntitiesSubscriber implements Common\EventSubscriber
{

	private const ACTION_CREATED = 'created';
	private const ACTION_UPDATED = 'updated';
	private const ACTION_DELETED = 'deleted';

	use Nette\SmartObject;

	/** @var Models\States\Devices\IPropertiesManager */
	private $devicesPropertiesStatesManager;

	/** @var Models\States\Devices\IPropertyRepository */
	private $devicesPropertyStateRepository;

	/** @var Models\States\Channels\IPropertiesManager */
	private $channelsPropertiesStatesManager;

	/** @var Models\States\Channels\IPropertyRepository */
	private $channelPropertyStateRepository;

	/** @var NodeExchangePublishers\IRabbitMqPublisher */
	private $publisher;

	/** @var ORM\EntityManagerInterface */
	private $entityManager;

	public function __construct(
		Models\States\Devices\IPropertiesManager $devicesPropertiesStatesManager,
		Models\States\Devices\IPropertyRepository $devicesPropertyStateRepository,
		Models\States\Channels\IPropertiesManager $channelsPropertiesStatesManager,
		Models\States\Channels\IPropertyRepository $channelPropertyStateRepository,
		NodeExchangePublishers\IRabbitMqPublisher $publisher,
		ORM\EntityManagerInterface $entityManager
	) {
		$this->devicesPropertiesStatesManager = $devicesPropertiesStatesManager;
		$this->devicesPropertyStateRepository = $devicesPropertyStateRepository;
		$this->channelsPropertiesStatesManager = $channelsPropertiesStatesManager;
		$this->channelPropertyStateRepository = $channelPropertyStateRepository;

		$this->publisher = $publisher;
		$this->entityManager = $entityManager;
	}

	/**
	 * Register events
	 *
	 * @return string[]
	 */
	public function getSubscribedEvents(): array
	{
		return [
			ORM\Events::onFlush,
			ORM\Events::postPersist,
			ORM\Events::postUpdate,
		];
	}

	/**
	 * @param ORM\Event\LifecycleEventArgs $eventArgs
	 *
	 * @return void
	 */
	public function postPersist(ORM\Event\LifecycleEventArgs $eventArgs): void
	{
		// onFlush was executed before, everything already initialized
		$entity = $eventArgs->getObject();

		// Check for valid entity
		if (!$entity instanceof NodeDatabaseEntities\IEntity) {
			return;
		}

		$this->processEntityAction($entity, self::ACTION_CREATED);
	}

	/**
	 * @param ORM\Event\LifecycleEventArgs $eventArgs
	 *
	 * @return void
	 */
	public function postUpdate(ORM\Event\LifecycleEventArgs $eventArgs): void
	{
		$uow = $this->entityManager->getUnitOfWork();

		// onFlush was executed before, everything already initialized
		$entity = $eventArgs->getObject();

		// Get changes => should be already computed here (is a listener)
		$changeset = $uow->getEntityChangeSet($entity);

		// If we have no changes left => don't create revision log
		if (count($changeset) === 0) {
			return;
		}

		// Check for valid entity
		if (!$entity instanceof NodeDatabaseEntities\IEntity) {
			return;
		}

		$this->processEntityAction($entity, self::ACTION_UPDATED);
	}

	/**
	 * @return void
	 */
	public function onFlush(): void
	{
		$uow = $this->entityManager->getUnitOfWork();

		$processedEntities = [];

		foreach ($uow->getScheduledEntityDeletions() as $entity) {
			// Doctrine is fine deleting elements multiple times. We are not.
			$hash = $this->getHash($entity, $uow->getEntityIdentifier($entity));

			if (in_array($hash, $processedEntities, true)) {
				continue;
			}

			$processedEntities[] = $hash;

			// Check for valid entity
			if (!$entity instanceof NodeDatabaseEntities\IEntity) {
				continue;
			}

			$this->processEntityAction($entity, self::ACTION_DELETED);
		}
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $entity
	 * @param mixed[] $identifier
	 *
	 * @return string
	 */
	private function getHash(NodeDatabaseEntities\IEntity $entity, array $identifier): string
	{
		return implode(
			' ',
			array_merge(
				[$this->getRealClass(get_class($entity))],
				$identifier
			)
		);
	}

	/**
	 * @param string $class
	 *
	 * @return string
	 */
	private function getRealClass(string $class): string
	{
		$pos = strrpos($class, '\\' . Persistence\Proxy::MARKER . '\\');

		if ($pos === false) {
			return $class;
		}

		return substr($class, $pos + Persistence\Proxy::MARKER_LENGTH + 2);
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $entity
	 * @param string $action
	 *
	 * @return void
	 */
	private function processEntityAction(NodeDatabaseEntities\IEntity $entity, string $action): void
	{
		if ($entity instanceof Entities\Devices\Properties\IProperty) {
			$state = $this->devicesPropertyStateRepository->findOne($entity->getId());

			switch ($action) {
				case self::ACTION_CREATED:
				case self::ACTION_UPDATED:
					if ($state === null) {
						$this->devicesPropertiesStatesManager->create($entity);
					}
					break;

				case self::ACTION_DELETED:
					if ($state !== null) {
						$this->devicesPropertiesStatesManager->delete($state);
					}
					break;
			}

		} elseif ($entity instanceof Entities\Channels\Properties\IProperty) {
			$state = $this->channelPropertyStateRepository->findOne($entity->getId());

			switch ($action) {
				case self::ACTION_CREATED:
				case self::ACTION_UPDATED:
					if ($state === null) {
						$this->channelsPropertiesStatesManager->create($entity);
					}
					break;

				case self::ACTION_DELETED:
					if ($state !== null) {
						$this->channelsPropertiesStatesManager->delete($state);
					}
					break;
			}
		}

		foreach (DevicesNode\Constants::RABBIT_MQ_ENTITIES_ROUTING_KEYS_MAPPING as $class => $routingKey) {
			if (get_class($entity) === $class) {
				$routingKey = str_replace(DevicesNode\Constants::RABBIT_MQ_ENTITIES_ROUTING_KEY_ACTION_REPLACE_STRING, $action, $routingKey);

				if (
					$entity instanceof Entities\Devices\Properties\IProperty
					|| $entity instanceof Entities\Channels\Properties\IProperty
				) {
					$state = null;

					if ($entity instanceof Entities\Devices\Properties\IProperty) {
						$state = $this->devicesPropertyStateRepository->findOne($entity->getId());

					} elseif ($entity instanceof Entities\Channels\Properties\IProperty) {
						$state = $this->channelPropertyStateRepository->findOne($entity->getId());
					}

					$this->publisher->publish($routingKey, array_merge($state !== null ? $state->toArray() : [], $this->toArray($entity)));

				} else {
					$this->publisher->publish($routingKey, $this->toArray($entity));
				}

				return;
			}

			if (is_subclass_of($entity, $class)) {
				$routingKey = str_replace(DevicesNode\Constants::RABBIT_MQ_ENTITIES_ROUTING_KEY_ACTION_REPLACE_STRING, $action, $routingKey);

				if (
					$entity instanceof Entities\Devices\Properties\IProperty
					|| $entity instanceof Entities\Channels\Properties\IProperty
				) {
					$state = null;

					if ($entity instanceof Entities\Devices\Properties\IProperty) {
						$state = $this->devicesPropertyStateRepository->findOne($entity->getId());

					} elseif ($entity instanceof Entities\Channels\Properties\IProperty) {
						$state = $this->channelPropertyStateRepository->findOne($entity->getId());
					}

					$this->publisher->publish($routingKey, array_merge($state !== null ? $state->toArray() : [], $this->toArray($entity)));

				} else {
					$this->publisher->publish($routingKey, $this->toArray($entity));
				}

				return;
			}
		}
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $entity
	 *
	 * @return mixed[]
	 */
	private function toArray(NodeDatabaseEntities\IEntity $entity): array
	{
		if (method_exists($entity, 'toArray')) {
			return $entity->toArray();
		}

		$metadata = $this->entityManager->getClassMetadata(get_class($entity));

		$fields = [];

		foreach ($metadata->fieldMappings as $field) {
			if (isset($field['fieldName'])) {
				$fields[] = $field['fieldName'];
			}
		}

		try {
			$rc = new ReflectionClass(get_class($entity));

			foreach ($rc->getProperties() as $property) {
				$fields[] = $property->getName();
			}

		} catch (ReflectionException $ex) {
			// Nothing to do, reflection could not be loaded
		}

		$fields = array_unique($fields);

		$values = [];

		foreach ($fields as $field) {
			try {
				$value = $this->getPropertyValue($entity, $field);

				if ($value instanceof Consistence\Enum\Enum) {
					$value = $value->getValue();

				} elseif ($value instanceof Uuid\UuidInterface) {
					$value = $value->toString();
				}

				if (is_object($value)) {
					continue;
				}

				$values[strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $field))] = $value;

			} catch (Exceptions\PropertyNotExistsException $ex) {
				// No need to do anything
			}
		}

		return $values;
	}

	/**
	 * @param NodeDatabaseEntities\IEntity $entity
	 * @param string $property
	 *
	 * @return mixed
	 *
	 * @throws Exceptions\PropertyNotExistsException
	 */
	private function getPropertyValue(NodeDatabaseEntities\IEntity $entity, string $property)
	{
		$ucFirst = ucfirst($property);

		$methods = [
			'get' . $ucFirst,
			'is' . $ucFirst,
			'has' . $ucFirst,
		];

		foreach ($methods as $method) {
			$callable = [$entity, $method];

			if (is_callable($callable)) {
				return call_user_func($callable);
			}
		}

		if (!property_exists($entity, $property)) {
			throw new Exceptions\PropertyNotExistsException(sprintf('Property "%s" does not exists on entity', $property));
		}

		return $entity->{$property};
	}

}
