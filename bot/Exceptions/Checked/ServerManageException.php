<?php declare(strict_types=1);


namespace PZBot\Exceptions\Checked;

use Longman\TelegramBot\TelegramLog;

class ServerManageException extends CheckedException
{
  const CODE = 403;

  public function __construct(string $reason)
  {
    TelegramLog::warning("Server manage exception cause: {$reason}");

    parent::__construct($reason, self::CODE, null);
  }
}