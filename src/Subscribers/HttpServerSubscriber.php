<?php declare(strict_types = 1);

/**
 * HttpServerSubscriber.php
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
use FastyBird\NodeWebServer\Events as NodeWebServerEvents;
use Nette;
use Symfony\Component\EventDispatcher;

/**
 * HTTP server events
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Subscribers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class HttpServerSubscriber implements EventDispatcher\EventSubscriberInterface
{

	use Nette\SmartObject;

	/** @var Common\Persistence\ManagerRegistry */
	private $managerRegistry;

	public function __construct(
		Common\Persistence\ManagerRegistry $managerRegistry
	) {
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * Register events
	 *
	 * @return mixed[]
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			NodeWebServerEvents\ResponseEvent::class        => 'onResponse',
			NodeWebServerEvents\ConsumerMessageEvent::class => 'onConsumedMessage',
		];
	}

	/**
	 * @return void
	 */
	public function onResponse(): void
	{
		$this->resetEntityManager();
	}

	/**
	 * @return void
	 */
	public function onConsumedMessage(): void
	{
		$this->resetEntityManager();
	}

	/**
	 * @return void
	 */
	public function resetEntityManager(): void
	{
		// Flushing and then clearing Doctrine's entity manager allows
		// for more memory to be released by PHP
		$this->managerRegistry->getManager()->flush();
		$this->managerRegistry->getManager()->clear();

		// Just in case PHP would choose not to run garbage collection,
		// we run it manually at the end of each batch so that memory is
		// regularly released
		gc_collect_cycles();
	}

}
