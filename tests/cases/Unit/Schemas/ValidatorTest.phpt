<?php declare(strict_types = 1);

namespace Tests\Cases;

use FastyBird\DevicesNode\Exceptions;
use FastyBird\NodeMetadata;
use FastyBird\NodeMetadata\Schemas as NodeMetadataSchemas;
use Nette\Utils;
use Ninjify\Nunjuck\TestCase\BaseTestCase;
use Tester\Assert;

require_once __DIR__ . '/../../../bootstrap.php';

/**
 * @testCase
 */
final class ValidatorTest extends BaseTestCase
{

	/**
	 * @param mixed[] $input
	 *
	 * @dataProvider ./../../../fixtures/Schemas/validateDevicePropertyValid.php
	 */
	public function testValidateDeviceProperty(array $input): void
	{
		$validator = new NodeMetadataSchemas\Validator();

		$schema = file_get_contents(NodeMetadata\Constants::RESOURCES_FOLDER . '/schemas/mqtt-node/data.device.property.json');

		if ($schema === false) {
			throw new Exceptions\InvalidStateException('Schema could not be loaded');
		}

		$result = $validator->validate(Utils\Json::encode($input), $schema);

		Assert::type(Utils\ArrayHash::class, $result);

		foreach ($input as $key => $value) {
			Assert::same($value, $result->offsetGet($key));
		}
	}

	/**
	 * @param mixed[] $input
	 *
	 * @dataProvider ./../../../fixtures/Schemas/validateDevicePropertyInvalid.php
	 *
	 * @throws FastyBird\NodeMetadata\Exceptions\InvalidDataException
	 */
	public function testValidateDevicePropertyInvalid(array $input): void
	{
		$validator = new NodeMetadataSchemas\Validator();

		$schema = file_get_contents(NodeMetadata\Constants::RESOURCES_FOLDER . '/schemas/mqtt-node/data.device.property.json');

		if ($schema === false) {
			throw new Exceptions\InvalidStateException('Schema could not be loaded');
		}

		$validator->validate(Utils\Json::encode($input), $schema);
	}

	/**
	 * @param mixed[] $input
	 *
	 * @dataProvider ./../../../fixtures/Schemas/validateChannelPropertyValid.php
	 */
	public function testValidateChannelProperty(array $input): void
	{
		$validator = new NodeMetadataSchemas\Validator();

		$schema = file_get_contents(NodeMetadata\Constants::RESOURCES_FOLDER . '/schemas/mqtt-node/data.channel.property.json');

		if ($schema === false) {
			throw new Exceptions\InvalidStateException('Schema could not be loaded');
		}

		$result = $validator->validate(Utils\Json::encode($input), $schema);

		Assert::type(Utils\ArrayHash::class, $result);

		foreach ($input as $key => $value) {
			Assert::same($value, $result->offsetGet($key));
		}
	}

	/**
	 * @param mixed[] $input
	 *
	 * @dataProvider ./../../../fixtures/Schemas/validateChannelPropertyInvalid.php
	 *
	 * @throws FastyBird\NodeMetadata\Exceptions\InvalidDataException
	 */
	public function testValidateChannelPropertyInvalid(array $input): void
	{
		$validator = new NodeMetadataSchemas\Validator();

		$schema = file_get_contents(NodeMetadata\Constants::RESOURCES_FOLDER . '/schemas/mqtt-node/data.channel.property.json');

		if ($schema === false) {
			throw new Exceptions\InvalidStateException('Schema could not be loaded');
		}

		$validator->validate(Utils\Json::encode($input), $schema);
	}

}

$test_case = new ValidatorTest();
$test_case->run();
