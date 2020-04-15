<?php declare(strict_types = 1);

/**
 * DbErrorMiddleware.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Middleware
 * @since          0.1.0
 *
 * @date           13.04.20
 */

namespace FastyBird\DevicesNode\Middleware;

use Doctrine;
use FastyBird\NodeLibs\Exceptions as NodeLibsExceptions;
use IPub\DoctrineOrmQuery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Catch database errors
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Middleware
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class DbErrorMiddleware implements MiddlewareInterface
{

	/**
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 *
	 * @throws NodeLibsExceptions\TerminateException
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		try {
			return $handler->handle($request);

		} catch (Doctrine\DBAL\DBALException $ex) {
			throw new NodeLibsExceptions\TerminateException('Database error: ' . $ex->getMessage(), $ex->getCode(), $ex);

		} catch (DoctrineOrmQuery\Exceptions\QueryException $ex) {
			throw new NodeLibsExceptions\TerminateException('Database error: ' . $ex->getMessage(), $ex->getCode(), $ex);
		}
	}

}
