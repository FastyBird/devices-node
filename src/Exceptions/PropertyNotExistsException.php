<?php declare(strict_types = 1);

/**
 * PropertyNotExistsException.php
 *
 * @license        More in license.md
 * @copyright      https://fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Exceptions
 * @since          0.1.0
 *
 * @date           22.03.20
 */

namespace FastyBird\DevicesNode\Exceptions;

use Exception as PHPException;

class PropertyNotExistsException extends PHPException implements IException
{

}
