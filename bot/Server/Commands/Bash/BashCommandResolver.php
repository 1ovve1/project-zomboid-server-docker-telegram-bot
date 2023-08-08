<?php declare(strict_types=1);

namespace PZBot\Server\Commands\Bash;

use PZBot\Server\Commands\AbstractCommandResolver;
use PZBot\Server\Commands\CommandListEnum;
use PZBot\Server\Commands\CommandResolverInterface;

class BashCommandResolver implements CommandResolverInterface
{
  private bool $exitCodeFlag;

  public function __construct(bool $exitCodeFlag = true) 
  {
    $this->exitCodeFlag = $exitCodeFlag;
  }

  function resolve(CommandListEnum $commandEnum): string 
  {
    $strCommand = $this->fromCommandToString($commandEnum);

    if ($this->exitCodeFlag) {
      return $this->withExitCode($strCommand, $commandEnum->exitCode());
    }
    
    return $strCommand;
  }

  function fromCommandToString(CommandListEnum $commandEnum): string
  {
    return match($commandEnum) {
      CommandListEnum::SERVER_DOWN => "docker-compose down",
      CommandListEnum::SERVER_UP => "docker-compose up -d",
      CommandListEnum::SERVER_RESTART => "docker-compose down && docker-compose up -d",
      CommandListEnum::GAME_LOGS_DELETE => "rm ./data/Logs/*.txt 2> /dev/null",
    };
  }

  protected function withExitCode(string $strCommand, string|int $exitCode) 
  {
    return sprintf("%s && echo '%s'", $strCommand, $exitCode);
  }
}