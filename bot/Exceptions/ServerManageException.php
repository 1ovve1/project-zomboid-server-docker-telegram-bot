<?php declare(strict_types=1);


namespace PZBot\Exceptions;

use Longman\TelegramBot\TelegramLog;
use RuntimeException;

class ServerManageException extends RuntimeException
{
  const CODE = 403;

  public function __construct($reason)
  {
    TelegramLog::warning("Server manage exception cause: {$reason}");

    parent::__construct($reason, self::CODE, null);
  }
}