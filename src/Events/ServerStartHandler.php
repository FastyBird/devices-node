<?php declare(strict_types = 1);

/**
 * ServerStartHandler.php
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
 * Http server start handler
 *
 * @package         FastyBird:DevicesNode!
 * @subpackage      Events
 *
 * @author          Adam Kadlec <adam.kadlec@fastybird.com>
 */
class ServerStartHandler
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
				$em->getConnection()->ping();
			}

		} catch (DBAL\DBALException $ex) {
			throw new NodeLibsExceptions\TerminateException('Database error: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}
	}

}
