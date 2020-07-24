<?php declare(strict_types = 1);

/**
 * AccountMiddleware.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Middleware
 * @since          0.1.0
 *
 * @date           24.07.20
 */

namespace FastyBird\DevicesNode\Middleware;

use Contributte\Translation;
use FastyBird\NodeAuth\Exceptions as NodeAuthExceptions;
use FastyBird\NodeJsonApi\Exceptions as NodeJsonApiExceptions;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Access check middleware
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Middleware
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class AccessMiddleware implements MiddlewareInterface
{

	/** @var Translation\Translator */
	private $translator;

	public function __construct(
		Translation\Translator $translator
	) {
		$this->translator = $translator;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param RequestHandlerInterface $handler
	 *
	 * @return ResponseInterface
	 *
	 * @throws NodeJsonApiExceptions\JsonApiErrorException
	 * @throws Translation\Exceptions\InvalidArgument
	 */
	public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
	{
		try {
			return $handler->handle($request);

		} catch (NodeAuthExceptions\UnauthorizedAccessException $ex) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_UNAUTHORIZED,
				$this->translator->translate('//node.base.messages.notAuthorized.heading'),
				$this->translator->translate('//node.base.messages.notAuthorized.message')
			);

		} catch (NodeAuthExceptions\ForbiddenAccessException $ex) {
			throw new NodeJsonApiExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_FORBIDDEN,
				$this->translator->translate('//node.base.messages.forbidden.heading'),
				$this->translator->translate('//node.base.messages.forbidden.message')
			);
		}
	}

}
