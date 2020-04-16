<?php declare(strict_types = 1);

/**
 * AuthenticateV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Controllers
 * @since          0.1.3
 *
 * @date           07.04.20
 */

namespace FastyBird\DevicesNode\Controllers;

use FastyBird\DevicesNode\Models;
use FastyBird\DevicesNode\Queries;
use FastyBird\NodeWebServer\Http as NodeWebServerHttp;
use Fig\Http\Message\StatusCodeInterface;
use Nette\Utils;
use Psr\Http\Message;
use stdClass;

/**
 * Device authentication controller
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
final class AuthenticateV1Controller extends BaseV1Controller
{

	/** @var Models\Devices\Credentials\ICredentialsRepository */
	private $credentialsRepository;

	public function __construct(
		Models\Devices\Credentials\ICredentialsRepository $credentialsRepository
	) {
		$this->credentialsRepository = $credentialsRepository;
	}

	/**
	 * @param Message\ServerRequestInterface $request
	 * @param NodeWebServerHttp\Response $response
	 *
	 * @return NodeWebServerHttp\Response
	 */
	public function vernemq(
		Message\ServerRequestInterface $request,
		NodeWebServerHttp\Response $response
	): NodeWebServerHttp\Response {
		try {
			$data = Utils\ArrayHash::from(Utils\Json::decode($request->getBody()->getContents(), Utils\Json::FORCE_ARRAY));

		} catch (Utils\JsonException $ex) {
			/** @var NodeWebServerHttp\Response $response */
			$response = $response
				->withStatus(StatusCodeInterface::STATUS_FORBIDDEN);

			return $response;
		}

		$username = $data->offsetExists('username') && $data->offsetGet('username') !== null ? (string) $data->offsetGet('username') : null;
		$password = $data->offsetExists('password') && $data->offsetGet('password') !== null ? (string) $data->offsetGet('password') : null;

		if (
			$username === null
			|| $username === ''
			|| $password === null
			|| $password === ''
		) {
			/** @var NodeWebServerHttp\Response $response */
			$response = $response
				->withStatus(StatusCodeInterface::STATUS_FORBIDDEN);

			return $response;
		}

		$findQuery = new Queries\FindDeviceCredentialsQuery();
		$findQuery->byUsername($username);
		$findQuery->byPassword($password);

		$credentials = $this->credentialsRepository->findOneBy($findQuery);

		if ($credentials === null) {
			/** @var NodeWebServerHttp\Response $response */
			$response = $response
				->withStatus(StatusCodeInterface::STATUS_FORBIDDEN);

			return $response;
		}

		$publishAcl = [];
		$subscribeAcl = [];

		if ($credentials->getDevice()->getParent() === null) {
			$publishRule = new stdClass();
			$publishRule->pattern = '/fb/+/' . $credentials->getDevice()->getIdentifier() . '/#';

			$publishAcl[] = $publishRule;

			$publishRule = new stdClass();
			$publishRule->pattern = '/fb/+/' . $credentials->getDevice()->getIdentifier() . '/$child/#';

			$publishAcl[] = $publishRule;

			$subscribeRule = new stdClass();
			$subscribeRule->pattern = '/fb/+/' . $credentials->getDevice()->getIdentifier() . '/#';

			$subscribeAcl[] = $subscribeRule;

			$subscribeRule = new stdClass();
			$subscribeRule->pattern = '/fb/+/' . $credentials->getDevice()->getIdentifier() . '/$child/#';

			$subscribeAcl[] = $subscribeRule;

		} else {
			$publishRule = new stdClass();
			$publishRule->pattern = '/fb/+/' . $credentials->getDevice()->getParent()->getIdentifier() . '/$child/' . $credentials->getDevice()->getIdentifier() . '/#';

			$publishAcl[] = $publishRule;

			$subscribeRule = new stdClass();
			$subscribeRule->pattern = '/fb/+/' . $credentials->getDevice()->getParent()->getIdentifier() . '/$child/' . $credentials->getDevice()->getIdentifier() . '/#';

			$subscribeAcl[] = $subscribeRule;
		}

		try {
			/** @var NodeWebServerHttp\Response $response */
			$response
				->getBody()
				->write(Utils\Json::encode([
					'result'        => 'ok',
					'publish_acl'   => $publishAcl,
					'subscribe_acl' => $subscribeAcl,
				]));

			return $response;

		} catch (Utils\JsonException $ex) {
			/** @var NodeWebServerHttp\Response $response */
			$response = $response
				->withStatus(StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);

			return $response;
		}
	}

}
