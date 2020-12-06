<?php declare(strict_types = 1);

/**
 * MessageHandler.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 * @since          0.1.0
 *
 * @date           06.12.20
 */

namespace FastyBird\DevicesNode\Consumers;

use FastyBird\ModulesMetadata;
use FastyBird\ModulesMetadata\Loaders as ModulesMetadataLoaders;
use FastyBird\RabbitMqPlugin\Consumers as RabbitMqPluginConsumers;
use Nette;
use Nette\Utils;
use Psr\Log;
use Throwable;

/**
 * Property message consumer
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Consumers
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
abstract class MessageHandler implements RabbitMqPluginConsumers\IMessageHandler
{

	use Nette\SmartObject;

	/** @var Log\LoggerInterface */
	protected $logger;

	/** @var ModulesMetadataLoaders\ISchemaLoader */
	private $schemaLoader;

	/** @var ModulesMetadata\Schemas\IValidator */
	private $validator;

	public function __construct(
		ModulesMetadataLoaders\ISchemaLoader $schemaLoader,
		ModulesMetadata\Schemas\IValidator $validator,
		?Log\LoggerInterface $logger = null
	) {
		$this->schemaLoader = $schemaLoader;
		$this->validator = $validator;

		$this->logger = $logger ?? new Log\NullLogger();
	}

	/**
	 * @param string $routingKey
	 * @param string $origin
	 * @param string $payload
	 *
	 * @return Utils\ArrayHash|null
	 */
	protected function parseMessage(
		string $routingKey,
		string $origin,
		string $payload
	): ?Utils\ArrayHash {
		$schemaFile = $this->getSchemaFile($routingKey, $origin);

		if ($schemaFile === null) {
			return null;
		}

		try {
			$schema = $this->schemaLoader->load($schemaFile);
			$message = $this->validator->validate($payload, $schema);

		} catch (Throwable $ex) {
			$this->logger->error('[FB:NODE:CONSUMER] ' . $ex->getMessage(), [
				'exception' => [
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
				],
			]);

			return null;
		}

		return $message;
	}

	/**
	 * @param string $routingKey
	 * @param string $origin
	 *
	 * @return string|null
	 */
	abstract protected function getSchemaFile(string $routingKey, string $origin): ?string;

}
