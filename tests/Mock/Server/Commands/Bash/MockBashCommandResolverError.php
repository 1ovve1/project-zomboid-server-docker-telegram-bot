<?php declare(strict_types=1);

namespace PZBot\Tests\Mock\Server\Commands\Bash;

use PZBot\Server\Commands\Bash\BashCommandResolver;
use PZBot\Server\Commands\CommandListEnum;

class MockBashCommandResolverError extends BashCommandResolver
{
  function fromCommandToString(CommandListEnum $commandEnum): string
  {
    return match($commandEnum) {
      default => "exit",
    };
  }
}