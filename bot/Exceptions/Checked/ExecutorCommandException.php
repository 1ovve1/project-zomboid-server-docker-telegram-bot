<?php declare(strict_types=1);

namespace PZBot\Exceptions\Checked;

use Longman\TelegramBot\TelegramLog;
use PZBot\Exceptions\CheckedException;
use PZBot\Server\Commands\CommandListEnum;
use PZBot\Server\Commands\CommandResultObject;

class ExecutorCommandException extends CheckedException
{
  const CODE = 501;

  function __construct(CommandListEnum $command, string $rawCommand, CommandResultObject $commandResult)
  {
    $message = sprintf(
      "Faild to execute '%s' command.\nCommand:%s\nRaw result:\n%s",
      $command->value, $rawCommand, $commandResult->getRawResult()
    );

    TelegramLog::error($message);

    parent::__construct($message, self::CODE);
  }
}