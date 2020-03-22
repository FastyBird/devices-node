<?php declare(strict_types = 1);

namespace FastyBird\NodeWebServer\JsonApi;

use Neomerx\JsonApi\Contracts;

/**
 * @template T of object
 * @extends  Contracts\Schema\SchemaInterface<T>
 */
interface ISchema extends Contracts\Schema\SchemaInterface
{

}
