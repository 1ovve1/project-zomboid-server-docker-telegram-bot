<?php declare(strict_types=1);

namespace PZBot\Server\Commands;

use Longman\TelegramBot\TelegramLog;
use PZBot\Server\Commands\CommandListEnum;
use PZBot\Exceptions\Checked\ExecutorCommandException;
use PZBot\Server\Commands\CommandResultObject;

/**
 * Class BashExecutor.
 */
class Executor implements ExecutorInterface
{
  protected CommandResolverInterface $resolver;

  function __construct(CommandResolverInterface $resolver)
  {
    $this->resolver = $resolver;
  }

  function execute(CommandListEnum ...$commands): CommandResultObject
  {
    foreach ($commands as $command) {
      $commandResolve = $this->resolver->resolve($command);
  
      TelegramLog::info("Execute command:\n\t'{$commandResolve}'");
  
      $commandResult = new CommandResultObject(
        $command,
        shell_exec($commandResolve)
      );

      if ($commandResult->isBad()) {
        throw new ExecutorCommandException($command, $commandResolve, $commandResult);
      }
    }

    return $commandResult;
  }
}