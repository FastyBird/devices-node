<?php declare(strict_types = 1);

/**
 * RuntimeException.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Exceptions
 * @since          0.1.0
 *
 * @date           17.03.20
 */

namespace FastyBird\DevicesNode\Exceptions;

use RuntimeException as PHPRuntimeException;

class RuntimeException extends PHPRuntimeException implements IException
{

}
