<?php declare(strict_types = 1);

/**
 * RequestHandler.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Events
 * @since          0.1.0
 *
 * @date           15.04.20
 */

namespace FastyBird\DevicesNode\Events;

use Doctrine\Common;
use Doctrine\DBAL;
use Doctrine\ORM;
use FastyBird\NodeLibs\Exceptions as NodeLibsExceptions;
use Nette;

/**
 * After http request processed handler
 *
 * @package         FastyBird:DevicesNode!
 * @subpackage      Events
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
class RequestHandler
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
	 * @return void
	 *
	 * @throws NodeLibsExceptions\TerminateException
	 */
	public function __invoke(): void
	{
		try {
			$em = $this->managerRegistry->getManager();

			if ($em instanceof ORM\EntityManagerInterface) {
				if (!$em->isOpen() && method_exists($em, 'create')) {
					$em = $em->create(
						$em->getConnection(),
						$em->getConfiguration()
					);
				}

				$connection = $em->getConnection();

				if (!$connection->ping()) {
					$connection->close();
					$connection->connect();
				}

				// Make sure we don't work with outdated entities
				$em->clear();
			}

		} catch (DBAL\DBALException $ex) {
			throw new NodeLibsExceptions\TerminateException('Database error: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}
	}

}
