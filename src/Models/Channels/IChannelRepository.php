<?php declare(strict_types = 1);

/**
 * IChannelRepository.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 * @since          0.1.0
 *
 * @date           23.04.17
 */

namespace FastyBird\DevicesNode\Models\Channels;

use FastyBird\DevicesNode\Entities;
use FastyBird\DevicesNode\Queries;
use IPub\DoctrineOrmQuery;

/**
 * Channel channels repository interface
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Models
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
interface IChannelRepository
{

	/**
	 * @param Queries\FindChannelsQuery $queryObject
	 *
	 * @return Entities\Channels\IChannel|null
	 *
	 * @phpstan-template T of Entities\Channels\Channel
	 * @phpstan-param    Queries\FindChannelsQuery<T> $queryObject
	 */
	public function findOneBy(Queries\FindChannelsQuery $queryObject): ?Entities\Channels\IChannel;

	/**
	 * @param Queries\FindChannelsQuery $queryObject
	 *
	 * @return Entities\Channels\IChannel[]
	 *
	 * @phpstan-template T of Entities\Channels\Channel
	 * @phpstan-param    Queries\FindChannelsQuery<T> $queryObject
	 */
	public function findAllBy(Queries\FindChannelsQuery $queryObject): array;

	/**
	 * @param Queries\FindChannelsQuery $queryObject
	 *
	 * @return DoctrineOrmQuery\ResultSet
	 *
	 * @phpstan-template T of Entities\Channels\Channel
	 * @phpstan-param    Queries\FindChannelsQuery<T> $queryObject
	 * @phpstan-return   DoctrineOrmQuery\ResultSet<T>
	 */
	public function getResultSet(
		Queries\FindChannelsQuery $queryObject
	): DoctrineOrmQuery\ResultSet;

}
