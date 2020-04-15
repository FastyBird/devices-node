<?php declare(strict_types = 1);

/**
 * JsonApiMiddleware.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Middleware
 * @since          0.1.0
 *
 * @date           21.03.20
 */

namespace FastyBird\DevicesNode\Middleware;

use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use IPub\DoctrineOrmQuery;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

/**
 * Result set handling middleware for {JSON:API}
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Middleware
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class JsonApiMiddleware implements MiddlewareInterface
{

	/**
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 *
	 * @throws Throwable
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		$response = $handler->handle($request);

		if ($response instanceof NodeWebServerHttp\Response) {
			$entity = $response->getEntity();

			if ($entity instanceof NodeWebServerHttp\ScalarEntity) {
				$data = $entity->getData();

				if ($data instanceof DoctrineOrmQuery\ResultSet) {
					if (array_key_exists('page', $request->getQueryParams())) {
						$queryParams = $request->getQueryParams();

						$pageOffset = isset($queryParams['page']['offset']) ? (int) $queryParams['page']['offset'] : null;
						$pageLimit = isset($queryParams['page']['limit']) ? (int) $queryParams['page']['limit'] : null;

					} else {
						$pageOffset = null;
						$pageLimit = null;
					}

					if ($pageOffset !== null && $pageLimit !== null) {
						if ($data->getTotalCount() > $pageLimit) {
							$data->applyPaging($pageOffset, $pageLimit);
						}

						$response = $response
							->withAttribute(NodeWebServerHttp\ResponseAttributes::ATTR_TOTAL_COUNT, $data->getTotalCount())
							->withEntity(NodeWebServerHttp\ScalarEntity::from($data->toArray()));

					} else {
						$response = $response
							->withEntity(NodeWebServerHttp\ScalarEntity::from($data->toArray()));
					}
				}
			}
		}

		return $response;
	}

}
