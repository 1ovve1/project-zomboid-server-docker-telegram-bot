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
  protected bool $safeMod;

  /**
   * @param CommandResolverInterface $resolver
   * @param boolean $safeMod - throws ExecutorCommandException if command fails and $safeMod === true
   */
  function __construct(CommandResolverInterface $resolver, bool $safeMod = true)
  {
    $this->resolver = $resolver;
    $this->safeMod = $safeMod;
  }

  /**
   * @param CommandListEnum ...$commands
   * @return CommandResultObject|false
   * @throws ExecutorCommandException - if safeMod === true and command fails
   */
  function execute(CommandListEnum ...$commands): CommandResultObject|false
  {
    foreach ($commands as $command) {
      $commandResolve = $this->resolver->resolve($command);
  
      TelegramLog::info("Execute command:\n\t'{$commandResolve}'");
  
      $commandResult = new CommandResultObject(
        $command,
        shell_exec($commandResolve)
      );

      if ($this->safeMod && $commandResult->isBad()) {
        throw new ExecutorCommandException($command, $commandResolve, $commandResult);
      }
    }

    return $commandResult ?? false;
  }
}