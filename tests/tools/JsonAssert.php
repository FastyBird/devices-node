<?php declare(strict_types = 1);

namespace Tests\Tools;

use Closure;
use Nette\StaticClass;
use Nette\Utils;
use Tester\Assert;

class JsonAssert
{

	use StaticClass;

	/**
	 * @param string $fixturePath
	 * @param string $actualJson
	 * @param Closure|null $transformFixture
	 *
	 * @return void
	 */
	public static function assertFixtureMatch(
		string $fixturePath,
		string $actualJson,
		?Closure $transformFixture = null
	): void {
		$expectation = Utils\FileSystem::read($fixturePath);

		if ($transformFixture !== null) {
			$expectation = $transformFixture($expectation);
		}

		self::assertMatch($expectation, $actualJson);
	}

	/**
	 * @param string $expectedJson
	 * @param string $actualJson
	 * @param string|null $description
	 *
	 * @return void
	 */
	public static function assertMatch(
		string $expectedJson,
		string $actualJson,
		?string $description = null
	): void {
		Assert::equal(
			self::normalizeJson($expectedJson),
			self::normalizeJson($actualJson),
			$description
		);
	}

	/**
	 * @param string $json
	 *
	 * @return string
	 */
	private static function normalizeJson(string $json): string
	{
		try {
			ini_set('serialize_precision', '10');

			$data = Utils\Json::decode($json, Utils\Json::FORCE_ARRAY);
			$data = self::normalizeArrays($data);

			return Utils\Json::encode($data, Utils\Json::PRETTY);

		} catch (Utils\JsonException $ex) {
			return $json;

		} finally {
			ini_restore('serialize_precision');
		}
	}

	/**
	 * @param mixed $data
	 *
	 * @return mixed
	 */
	private static function normalizeArrays($data)
	{
		if (!is_array($data)) {
			return $data;
		}

		ksort($data);

		array_walk(
			$data,
			function (&$item): void {
				$item = self::normalizeArrays($item);
			}
		);

		return $data;
	}

}
