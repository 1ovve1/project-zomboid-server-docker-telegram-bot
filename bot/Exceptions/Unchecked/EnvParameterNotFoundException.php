<?php declare(strict_types=1);

namespace PZBot\Exceptions\Unchecked;
use Longman\TelegramBot\TelegramLog;
use PZBot\Exceptions\UncheckedException;

class EnvParameterNotFoundException extends UncheckedException
{
  const MESSAGE = "Param '%s' was not found in configuration .env file";

  public function __construct(string $paramName) 
  {
      $message = sprintf(self::MESSAGE, $paramName);

      TelegramLog::error($message);

      parent::__construct($message);
  }
}