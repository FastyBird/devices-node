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
use FastyBird\Database\Entities as DatabaseEntities;
use FastyBird\DevicesModule\Entities as DevicesModuleEntities;
use FastyBird\DevicesModule\Models as DevicesModuleModels;
use FastyBird\VerneMqAuthPlugin\Entities as VerneMqAuthPluginEntities;
use FastyBird\VerneMqAuthPlugin\Models as VerneMqAuthPluginModels;
use FastyBird\VerneMqAuthPlugin\Queries as VerneMqAuthPluginQueries;
use FastyBird\VerneMqAuthPlugin\Types as VerneMqAuthPluginTypes;
use Nette;
use Throwable;

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

	/** @var DevicesModuleModels\States\IPropertiesManager */
	private DevicesModuleModels\States\IPropertiesManager $propertiesStatesManager;

	/** @var DevicesModuleModels\States\IPropertyRepository */
	private DevicesModuleModels\States\IPropertyRepository $propertyStateRepository;

	/** @var VerneMqAuthPluginModels\Accounts\IAccountRepository */
	private VerneMqAuthPluginModels\Accounts\IAccountRepository $verneMqAccountRepository;

	/** @var ORM\EntityManagerInterface */
	private ORM\EntityManagerInterface $entityManager;

	public function __construct(
		DevicesModuleModels\States\IPropertiesManager $propertiesStatesManager,
		DevicesModuleModels\States\IPropertyRepository $propertyStateRepository,
		VerneMqAuthPluginModels\Accounts\IAccountRepository $verneMqAccountRepository,
		ORM\EntityManagerInterface $entityManager
	) {
		$this->propertiesStatesManager = $propertiesStatesManager;
		$this->propertyStateRepository = $propertyStateRepository;

		$this->verneMqAccountRepository = $verneMqAccountRepository;

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
			ORM\Events::preFlush,
			ORM\Events::preUpdate,
			ORM\Events::preRemove,
			ORM\Events::postPersist,
			ORM\Events::postUpdate,
		];
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
			if (
				!$entity instanceof DevicesModuleEntities\Devices\Properties\IProperty &&
				!$entity instanceof DevicesModuleEntities\Channels\Properties\IProperty
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
	 * @param ORM\Event\PreFlushEventArgs $eventArgs
	 *
	 * @return void
	 *
	 * @throws Throwable
	 */
	public function preFlush(ORM\Event\PreFlushEventArgs $eventArgs): void
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();

		// Check all scheduled updates
		foreach ($uow->getScheduledEntityInsertions() as $object) {
			if ($object instanceof DevicesModuleEntities\Devices\Credentials\Credentials) {
				$this->processCredentialsEntity($object, $em, $uow);
			}
		}
	}

	/**
	 * @param ORM\Event\PreUpdateEventArgs $eventArgs
	 *
	 * @return void
	 *
	 * @throws Throwable
	 */
	public function preUpdate(ORM\Event\PreUpdateEventArgs $eventArgs): void
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();

		// Check all scheduled updates
		foreach ($uow->getScheduledEntityUpdates() as $object) {
			if ($object instanceof DevicesModuleEntities\Devices\Credentials\Credentials) {
				$this->processCredentialsEntity($object, $em, $uow);
			}
		}
	}

	/**
	 * @param ORM\Event\LifecycleEventArgs $eventArgs
	 *
	 * @return void
	 *
	 * @throws Throwable
	 */
	public function preRemove(ORM\Event\LifecycleEventArgs $eventArgs): void
	{
		$em = $eventArgs->getEntityManager();
		$uow = $em->getUnitOfWork();

		foreach (array_merge($uow->getScheduledEntityDeletions(), $uow->getScheduledCollectionDeletions()) as $object) {
			if ($object instanceof DevicesModuleEntities\Devices\Credentials\Credentials) {
				$findAccount = new VerneMqAuthPluginQueries\FindAccountQuery();
				$findAccount->byUsername($object->getUsername());

				$accounts = $this->verneMqAccountRepository->findAllBy($findAccount);

				foreach ($accounts as $account) {
					$uow->scheduleForDelete($account);
				}
			}
		}
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
		if (
			!$entity instanceof DevicesModuleEntities\Devices\Properties\IProperty &&
			!$entity instanceof DevicesModuleEntities\Channels\Properties\IProperty
		) {
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
			(
				!$entity instanceof DevicesModuleEntities\Devices\Properties\IProperty &&
				!$entity instanceof DevicesModuleEntities\Channels\Properties\IProperty
			) || $uow->isScheduledForDelete($entity)
		) {
			return;
		}

		$this->processEntityAction($entity, self::ACTION_UPDATED);
	}

	/**
	 * @param DatabaseEntities\IEntity $entity
	 * @param string $action
	 *
	 * @return void
	 */
	private function processEntityAction(DatabaseEntities\IEntity $entity, string $action): void
	{
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
	 * @param DevicesModuleEntities\Devices\Credentials\Credentials $credentials
	 * @param ORM\EntityManager $em
	 * @param ORM\UnitOfWork $uow
	 *
	 * @return void
	 *
	 * @throws Throwable
	 */
	private function processCredentialsEntity(
		DevicesModuleEntities\Devices\Credentials\Credentials $credentials,
		ORM\EntityManager $em,
		ORM\UnitOfWork $uow
	): void {
		$changeSet = $uow->getEntityChangeSet($credentials);

		if (isset($changeSet['username']) && isset($changeSet['username'][0])) {
			$username = $changeSet['username'][0];

		} else {
			$username = $credentials->getUsername();
		}

		$account = $this->findAccount($username);

		if ($account === null) {
			$this->createAccount($credentials, $uow);

		} else {
			$this->updateAccount($account, $credentials, $em, $uow);
		}
	}

	/**
	 * @param string $username
	 *
	 * @return VerneMqAuthPluginEntities\Accounts\IAccount|null
	 */
	private function findAccount(
		string $username
	): ?VerneMqAuthPluginEntities\Accounts\IAccount {
		$findAccount = new VerneMqAuthPluginQueries\FindAccountQuery();
		$findAccount->byUsername($username);

		return $this->verneMqAccountRepository->findOneBy($findAccount);
	}

	/**
	 * @param DevicesModuleEntities\Devices\Credentials\Credentials $credentials
	 * @param ORM\UnitOfWork $uow
	 *
	 * @return void
	 *
	 * @throws Throwable
	 */
	private function createAccount(
		DevicesModuleEntities\Devices\Credentials\Credentials $credentials,
		ORM\UnitOfWork $uow
	): void {
		$account = new VerneMqAuthPluginEntities\Accounts\Account(
			$credentials->getUsername(),
			$credentials->getPassword(),
			VerneMqAuthPluginTypes\AccountType::get(VerneMqAuthPluginTypes\AccountType::TYPE_DEVICE)
		);

		$account->setClientId($credentials->getDevice()->getIdentifier());

		$account->addPublishAcl('/fb/+/' . $credentials->getUsername() . '/#');
		$account->addSubscribeAcl('/fb/+/' . $credentials->getUsername() . '/#');

		$uow->scheduleForInsert($account);
	}

	/**
	 * @param VerneMqAuthPluginEntities\Accounts\IAccount $account
	 * @param DevicesModuleEntities\Devices\Credentials\Credentials $credentials
	 * @param ORM\EntityManager $em
	 * @param ORM\UnitOfWork $uow
	 *
	 * @return void
	 */
	private function updateAccount(
		VerneMqAuthPluginEntities\Accounts\IAccount $account,
		DevicesModuleEntities\Devices\Credentials\Credentials $credentials,
		ORM\EntityManager $em,
		ORM\UnitOfWork $uow
	): void {
		$classMetadata = $em->getClassMetadata(get_class($account));

		$passwordProperty = $classMetadata->getReflectionProperty('password');
		$usernameProperty = $classMetadata->getReflectionProperty('username');

		$account->setPassword($credentials->getPassword());
		$account->setUsername($credentials->getUsername());

		$uow->propertyChanged(
			$account,
			'password',
			$passwordProperty->getValue($account),
			$credentials->getPassword()
		);

		$uow->propertyChanged(
			$account,
			'username',
			$usernameProperty->getValue($account),
			$credentials->getUsername()
		);

		$uow->scheduleExtraUpdate($account, [
			'password' => [
				$passwordProperty->getValue($account),
				$credentials->getPassword(),
			],
			'username' => [
				$usernameProperty->getValue($account),
				$credentials->getUsername(),
			],
		]);
	}

}
