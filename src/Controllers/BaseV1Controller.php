<?php declare(strict_types = 1);

/**
 * BaseV1Controller.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Controllers
 * @since          0.1.0
 *
 * @date           13.04.19
 */

namespace FastyBird\DevicesNode\Controllers;

use Contributte\Translation;
use Doctrine\DBAL\Connection;
use FastyBird\DevicesNode\Exceptions;
use FastyBird\NodeWebServer\Exceptions as NodeWebServerExceptions;
use Fig\Http\Message\StatusCodeInterface;
use IPub\JsonAPIDocument;
use Nette;
use Nette\Utils;
use Nettrine\ORM;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;

/**
 * API base controller
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Controllers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class BaseV1Controller
{

	use Nette\SmartObject;

	/** @var Translation\PrefixedTranslator */
	protected $translator;

	/** @var ORM\ManagerRegistry */
	protected $managerRegistry;

	/** @var LoggerInterface */
	protected $logger;

	/** @var string */
	protected $translationDomain = '';

	/**
	 * @param Translation\Translator $translator
	 *
	 * @return void
	 */
	public function injectTranslator(Translation\Translator $translator): void
	{
		$this->translator = new Translation\PrefixedTranslator($translator, $this->translationDomain);
	}

	/**
	 * @param ORM\ManagerRegistry $managerRegistry
	 *
	 * @return void
	 */
	public function injectManagerRegistry(ORM\ManagerRegistry $managerRegistry): void
	{
		$this->managerRegistry = $managerRegistry;
	}

	/**
	 * @param LoggerInterface $logger
	 *
	 * @return void
	 */
	public function injectLogger(LoggerInterface $logger): void
	{
		$this->logger = $logger;
	}

	/**
	 * @param ServerRequestInterface $request
	 *
	 * @return JsonAPIDocument\IDocument<JsonAPIDocument\Objects\StandardObject>
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	protected function createDocument(ServerRequestInterface $request): JsonAPIDocument\IDocument
	{
		try {
			$document = new JsonAPIDocument\Document(Utils\Json::decode($request->getBody()->getContents()));

		} catch (Utils\JsonException $ex) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.notValidJson.heading'),
				$this->translator->translate('//node.base.messages.notValidJson.message')
			);

		} catch (JsonAPIDocument\Exceptions\RuntimeException $ex) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_BAD_REQUEST,
				$this->translator->translate('//node.base.messages.notValidJsonApi.heading'),
				$this->translator->translate('//node.base.messages.notValidJsonApi.message')
			);
		}

		return $document;
	}

	/**
	 * @param string|null $relationEntity
	 *
	 * @throws NodeWebServerExceptions\IJsonApiException
	 */
	protected function throwUnknownRelation(?string $relationEntity): void
	{
		if ($relationEntity !== null) {
			throw new NodeWebServerExceptions\JsonApiErrorException(
				StatusCodeInterface::STATUS_NOT_FOUND,
				$this->translator->translate('//node.base.messages.relationNotFound.heading'),
				$this->translator->translate('//node.base.messages.relationNotFound.message', ['relation' => $relationEntity])
			);
		}

		throw new NodeWebServerExceptions\JsonApiErrorException(
			StatusCodeInterface::STATUS_NOT_FOUND,
			$this->translator->translate('//node.base.messages.unknownRelation.heading'),
			$this->translator->translate('//node.base.messages.unknownRelation.message')
		);
	}

	/**
	 * @return Connection
	 */
	protected function getOrmConnection(): Connection
	{
		$connection = $this->managerRegistry->getConnection();

		if ($connection instanceof Connection) {
			return $connection;
		}

		throw new Exceptions\RuntimeException('Entity manager could not be loaded');
	}

}
