<?php declare(strict_types = 1);

/**
 * EntitiesSubscriber.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Subscribers
 * @since          1.0.0
 *
 * @date           22.03.20
 */

namespace FastyBird\DevicesNode\Subscribers;

use Doctrine\Common;
use Doctrine\ORM;
use Doctrine\Persistence;
use FastyBird\DevicesNode;
use FastyBird\DevicesNode\Entities;
use FastyBird\NodeLibs\Publishers as NodeLibsPublishers;
use Nette;

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

	use Nette\SmartObject;

	/** @var NodeLibsPublishers\IRabbitMqPublisher */
	private $publisher;

	public function __construct(
		NodeLibsPublishers\IRabbitMqPublisher $publisher
	) {
		$this->publisher = $publisher;
	}

	/**
	 * Register events
	 *
	 * @return mixed[]
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
		$entity = $eventArgs->getEntity();

		// Check for valid entity
		if (!$entity instanceof Entities\IIdentifiedEntity) {
			return;
		}

		$this->processEntityAction($entity, 'CREATE');
	}

	/**
	 * @param ORM\Event\LifecycleEventArgs $eventArgs
	 *
	 * @return void
	 */
	public function postUpdate(ORM\Event\LifecycleEventArgs $eventArgs): void
	{
		$entityManager = $eventArgs->getEntityManager();
		$uow = $entityManager->getUnitOfWork();

		// onFlush was executed before, everything already initialized
		$entity = $eventArgs->getEntity();

		// Get changes => should be already computed here (is a listener)
		$changeset = $uow->getEntityChangeSet($entity);

		// If we have no changes left => don't create revision log
		if (count($changeset) === 0) {
			return;
		}

		// Check for valid entity
		if (!$entity instanceof Entities\IIdentifiedEntity) {
			return;
		}

		$this->processEntityAction($entity, 'UPDATE');
	}

	/**
	 * @param ORM\Event\OnFlushEventArgs $eventArgs
	 *
	 * @return void
	 */
	public function onFlush(ORM\Event\OnFlushEventArgs $eventArgs): void
	{
		$entityManager = $eventArgs->getEntityManager();
		$uow = $entityManager->getUnitOfWork();

		$processedEntities = [];

		foreach ($uow->getScheduledEntityDeletions() as $entity) {
			//doctrine is fine deleting elements multiple times. We are not.
			$hash = $this->getHash($entity, $uow->getEntityIdentifier($entity));

			if (in_array($hash, $processedEntities)) {
				continue;
			}

			$processedEntities[] = $hash;

			// Check for valid entity
			if (!$entity instanceof Entities\IIdentifiedEntity) {
				continue;
			}

			$this->processEntityAction($entity, 'DELETE');
		}
	}

	/**
	 * @param Entities\IIdentifiedEntity $entity
	 * @param mixed[] $identifier
	 *
	 * @return string
	 */
	private function getHash(Entities\IIdentifiedEntity $entity, array $identifier): string
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
	 * @param Entities\IIdentifiedEntity $entity
	 * @param string $action
	 *
	 * @return void
	 */
	private function processEntityAction(Entities\IIdentifiedEntity $entity, string $action): void
	{
		if (!array_key_exists(get_class($entity), DevicesNode\Constants::RABBIT_MQ_ENTITIES_ROUTING_KEYS)) {
			return;
		}

		$routingKey = DevicesNode\Constants::RABBIT_MQ_ENTITIES_ROUTING_KEYS[get_class($entity)];
		$routingKey = str_replace(DevicesNode\Constants::RABBIT_MQ_ENTITIES_ROUTING_KEY_ACTION_REPLACE_STRING, $action, $routingKey);

		$this->publisher->publish($routingKey, $entity->toSimpleArray());
	}

}
