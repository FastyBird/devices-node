<?php declare(strict_types = 1);

/**
 * TEntityParams.php
 *
 * @license        More in license.md
 * @copyright      https://www.fastybird.com
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 * @since          0.1.0
 *
 * @date           29.01.14
 */

namespace FastyBird\DevicesNode\Entities;

use Doctrine\ORM\Mapping as ORM;
use IPub\DoctrineCrud\Mapping\Annotation as IPubDoctrine;
use Nette\Utils;

/**
 * Entity params field trait
 *
 * @package        FastyBird:DevicesNode!
 * @subpackage     Entities
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
trait TEntityParams
{

	/**
	 * @var mixed[]
	 *
	 * @IPubDoctrine\Crud(is="writable")
	 * @ORM\Column(type="json", name="params", nullable=true)
	 */
	protected $params = [];

	/**
	 * {@inheritDoc}
	 */
	public function setParams(array $params): void
	{
		$this->params = array_merge($this->params, $params);
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParams(): Utils\ArrayHash
	{
		return Utils\ArrayHash::from($this->params);
	}

	/**
	 * {@inheritDoc}
	 */
	public function setParam(string $key, $value = null): void
	{
		$parts = explode('.', $key);

		if (count($parts) > 1) {
			$val = &$this->params;
			$last = array_pop($parts);

			foreach ($parts as $part) {
				if (!isset($val[$part]) || !is_array($val[$part])) {
					$val[$part] = [];
				}

				$val = &$val[$part];
			}

			if ($value === null) {
				unset($val[$last]);

			} else {
				$val[$last] = $value;
			}

		} else {
			if ($value === null) {
				unset($this->params[$parts[0]]);

			} else {
				$this->params[$parts[0]] = $value;
			}
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getParam(string $key, $default = null)
	{
		$parts = explode('.', $key);

		if (array_key_exists($parts[0], $this->params)) {
			if (is_array($this->params[$parts[0]]) || $this->params[$parts[0]] instanceof Utils\ArrayHash) {
				$val = null;

				foreach ($parts as $part) {
					if (isset($val)) {
						if (isset($val[$part])) {
							$val = $val[$part];

						} else {
							$val = null;
						}

					} else {
						$val = $this->params[$part] ?? $default;
					}
				}

				return $val ?? $default;

			} else {
				return is_string($this->params[$parts[0]]) ? trim($this->params[$parts[0]]) : $this->params[$parts[0]];
			}
		}

		return $default;
	}

}
