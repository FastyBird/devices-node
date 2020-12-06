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
use FastyBird\CouchDbStoragePlugin\Models as CouchDbStoragePluginModels;
use FastyBird\Database\Entities as DatabaseEntities;
use FastyBird\DevicesModule;
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\RabbitMqPlugin\Publishers as RabbitMqPluginPublishers;
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

	/** @var CouchDbStoragePluginModels\IPropertiesManager */
	private $propertiesStatesManager;

	/** @var CouchDbStoragePluginModels\IPropertyRepository */
	private $propertyStateRepository;

	/** @var RabbitMqPluginPublishers\IRabbitMqPublisher */
	private $publisher;

	/** @var ORM\EntityManagerInterface */
	private $entityManager;

	public function __construct(
		CouchDbStoragePluginModels\IPropertiesManager $propertiesStatesManager,
		CouchDbStoragePluginModels\IPropertyRepository $propertyStateRepository,
		RabbitMqPluginPublishers\IRabbitMqPublisher $publisher,
		ORM\EntityManagerInterface $entityManager
	) {
		$this->propertiesStatesManager = $propertiesStatesManager;
		$this->propertyStateRepository = $propertyStateRepository;

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
			ORM\Events::preFlush,
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
		if (!$entity instanceof DatabaseEntities\IEntity) {
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
		if (
			!$entity instanceof DatabaseEntities\IEntity
			|| $uow->isScheduledForDelete($entity)
		) {
			return;
		}

		if (
			$entity instanceof DevicesModuleEntities\Channels\Controls\IControl
			&& $uow->isScheduledForUpdate($entity->getChannel())
		) {
			return;
		}

		if (
			$entity instanceof DevicesModuleEntities\Devices\Controls\IControl
			&& $uow->isScheduledForUpdate($entity->getDevice())
		) {
			return;
		}

		$this->processEntityAction($entity, self::ACTION_UPDATED);
	}

	/**
	 * @return void
	 */
	public function preFlush(): void
	{
		$uow = $this->entityManager->getUnitOfWork();

		foreach ($uow->getScheduledEntityDeletions() as $entity) {
			if (
				(
					$entity instanceof DevicesModuleEntities\Devices\Controls\IControl
					|| $entity instanceof DevicesModuleEntities\Channels\Controls\IControl
				)
				&& $entity->getName() === DevicesModule\Constants::CONTROL_CONFIG
			) {
				if ($entity instanceof DevicesModuleEntities\Devices\Controls\IControl) {
					foreach ($entity->getDevice()->getConfiguration() as $row) {
						$uow->scheduleForDelete($row);
					}
				}

				if ($entity instanceof DevicesModuleEntities\Channels\Controls\IControl) {
					foreach ($entity->getChannel()->getConfiguration() as $row) {
						$uow->scheduleForDelete($row);
					}
				}
			}
		}
	}

	/**
	 * @return void
	 */
	public function onFlush(): void
	{
		$uow = $this->entityManager->getUnitOfWork();

		$processedEntities = [];

		$processEntities = [];

		foreach ($uow->getScheduledEntityDeletions() as $entity) {
			// Doctrine is fine deleting elements multiple times. We are not.
			$hash = $this->getHash($entity, $uow->getEntityIdentifier($entity));

			if (in_array($hash, $processedEntities, true)) {
				continue;
			}

			$processedEntities[] = $hash;

			// Check for valid entity
			if (!$entity instanceof DatabaseEntities\IEntity) {
				continue;
			}

			if (
				$entity instanceof DevicesModuleEntities\Devices\Controls\IControl
				&& $uow->isScheduledForDelete($entity->getDevice())
			) {
				continue;
			}

			if (
				$entity instanceof DevicesModuleEntities\Channels\Controls\IControl
				&& $uow->isScheduledForDelete($entity->getChannel())
			) {
				continue;
			}

			$processEntities[] = $entity;
		}

		foreach ($processEntities as $entity) {
			$this->processEntityAction($entity, self::ACTION_DELETED);
		}
	}

	/**
	 * @param DatabaseEntities\IEntity $entity
	 * @param mixed[] $identifier
	 *
	 * @return string
	 */
	private function getHash(DatabaseEntities\IEntity $entity, array $identifier): string
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
	 * @param DatabaseEntities\IEntity $entity
	 * @param string $action
	 *
	 * @return void
	 */
	private function processEntityAction(DatabaseEntities\IEntity $entity, string $action): void
	{
		if ($entity instanceof DevicesModuleEntities\Devices\Controls\IControl) {
			$entity = $entity->getDevice();
			$action = self::ACTION_UPDATED;
		}

		if ($entity instanceof DevicesModuleEntities\Channels\Controls\IControl) {
			$entity = $entity->getChannel();
			$action = self::ACTION_UPDATED;
		}

		if (
			$entity instanceof DevicesModuleEntities\Devices\Properties\IProperty ||
			$entity instanceof DevicesModuleEntities\Channels\Properties\IProperty
		) {
			$state = $this->propertyStateRepository->findOne($entity->getId());

			switch ($action) {
				case self::ACTION_CREATED:
				case self::ACTION_UPDATED:
					if ($state === null) {
						$this->propertiesStatesManager->create($entity->getId(), Nette\Utils\ArrayHash::from($entity->toArray()));
					}
					break;

				case self::ACTION_DELETED:
					if ($state !== null) {
						$this->propertiesStatesManager->delete($state);
					}
					break;
			}
		}

		foreach (DevicesNode\Constants::RABBIT_MQ_ENTITIES_ROUTING_KEYS_MAPPING as $class => $routingKey) {
			if (
			$this->validateEntity($entity, $class)
			) {
				$routingKey = str_replace(DevicesNode\Constants::RABBIT_MQ_ENTITIES_ROUTING_KEY_ACTION_REPLACE_STRING, $action, $routingKey);

				if (
					$entity instanceof DevicesModuleEntities\Devices\Properties\IProperty
					|| $entity instanceof DevicesModuleEntities\Channels\Properties\IProperty
				) {
					$state = $this->propertyStateRepository->findOne($entity->getId());

					$this->publisher->publish($routingKey, array_merge($state !== null ? $state->toArray() : [], $this->toArray($entity)));

				} else {
					$this->publisher->publish($routingKey, $this->toArray($entity));
				}

				return;
			}
		}
	}

	/**
	 * @param DatabaseEntities\IEntity $entity
	 * @param string $class
	 *
	 * @return bool
	 */
	private function validateEntity(DatabaseEntities\IEntity $entity, string $class): bool
	{
		$result = false;

		if (get_class($entity) === $class) {
			$result = true;
		}

		if (is_subclass_of($entity, $class)) {
			$result = true;
		}

		return $result;
	}

	/**
	 * @param DatabaseEntities\IEntity $entity
	 *
	 * @return mixed[]
	 */
	private function toArray(DatabaseEntities\IEntity $entity): array
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

				$key = preg_replace('/(?<!^)[A-Z]/', '_$0', $field);

				if ($key !== null) {
					$values[strtolower($key)] = $value;
				}

			} catch (Exceptions\PropertyNotExistsException $ex) {
				// No need to do anything
			}
		}

		return $values;
	}

	/**
	 * @param DatabaseEntities\IEntity $entity
	 * @param string $property
	 *
	 * @return mixed
	 *
	 * @throws Exceptions\PropertyNotExistsException
	 */
	private function getPropertyValue(DatabaseEntities\IEntity $entity, string $property)
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
