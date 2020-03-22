<?php declare(strict_types = 1);

/**
 * LogicException.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Exceptions
 * @since          0.1.0
 *
 * @date           13.03.20
 */

namespace FastyBird\DevicesNode\Exceptions;

use RuntimeException as PHPRuntimeException;

class LogicException extends PHPRuntimeException implements IException
{

}
