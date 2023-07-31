<?php declare(strict_types=1);

namespace PZBot\Server\Commands;

use PZBot\Exceptions\Checked\ExecutorCommandException;

interface ExecutorInterface
{
  /**
   * @param CommandListEnum ...$commands
   * @return CommandResultObject
   * @throws ExecutorCommandException
   */
  function execute(CommandListEnum ...$commands): CommandResultObject;
}