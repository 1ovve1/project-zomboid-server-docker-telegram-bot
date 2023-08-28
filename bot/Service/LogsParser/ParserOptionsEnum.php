<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser;

enum ParserOptionsEnum
{
  case FROM_TOP;
  case UNIQUE;
  case ONCE; 
  case DEFAULT;

  function isUnique(): bool
  {
    return $this === ParserOptionsEnum::UNIQUE;
  }

  function isOnce(): bool
  {
    return $this === ParserOptionsEnum::ONCE;
  }

  function isDefault(): bool
  {
    return $this === ParserOptionsEnum::DEFAULT;
  }

  function isFromTop(): bool
  {
    return $this === ParserOptionsEnum::FROM_TOP;
  }

}