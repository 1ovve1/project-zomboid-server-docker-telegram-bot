<?php declare(strict_types=1);

namespace PZBot\Config;
use PZBot\Exceptions\Unchecked\EnvParameterNotFoundException;

/**
 * Env wrapper
 */
class Env
{
  protected array $config = [];

  /**
   * @param array|null $params - if null object will be use $_ENV
   */
  function __construct(?array $params = null) {
    $this->config = $_ENV ?? $params;
  }

  /**
   * Find value in $_ENV use $paramName
   * $paramName can have a complex form with dots for deep search
   * Example: 'my.path.to.var' equals '$_ENV["my"]["path"]["to"]["var"]
   *
   * If otherwise was not given or null - throws uncheked exception EnvParameterNotFoundException
   * 
   * @param string $paramName - param name or path sepparated with dots
   * @param mixed $otherwise - value that will be used by default if paramName dosent exists
   * @return mixed
   */
  function get(string $paramName, mixed $otherwise = null): mixed
  {
    $path = explode('.', $paramName);
    $tree = $this->config;
    $branch = null;

    foreach($path as $name) {
      $branch = $tree[$name] ?? null;

      if ($branch === null) {
        break;
      }

      if (is_array($branch)) {
        $tree = $branch;
      } else {
        $tree = [];
      }
    }

    return $branch
          ?? $otherwise 
          ?? throw new EnvParameterNotFoundException($paramName);
  }
}