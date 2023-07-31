<?php declare(strict_types=1);

namespace PZBot\Server\Commands;

class CommandResultObject
{
  protected string|false|null $bashResult;

  function __construct(CommandLIstEnum $commandSource, string|false|null $result)
  {
    if (is_string($result)) {
      $result = trim($result);
    }
    
    $this->bashResult = $result;
  }

  function getRawResult(): string|false|null
  {
    return $this->bashResult;
  }

  function isOK(): bool
  {
    return $this->bashResult == true;
  }

  function isBad(): bool
  {
    return !$this->isOK();
  }
}