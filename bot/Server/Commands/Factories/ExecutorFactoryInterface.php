<?php declare(strict_types=1);

namespace PZBot\Server\Commands\Factories;

use PZBot\Server\Commands\ExecutorInterface;

interface ExecutorFactoryInterface
{
  /**
   * Return bash executor
   *
   * @return ExecutorInterface
   */
  function getExecutor(): ExecutorInterface;

  /**
   * Return bash executor with unsafe flag
   *
   * @return ExecutorInterface
   */
  function getExecutorUnsafe(): ExecutorInterface;
}