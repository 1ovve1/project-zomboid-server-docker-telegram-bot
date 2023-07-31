<?php declare(strict_types=1);


namespace PZBot\Exceptions\Checked;

use Longman\TelegramBot\TelegramLog;
use PZBot\Exceptions\CheckedException;
use Throwable;

class UnknownServerManagerError extends CheckedException
{
  const CODE = 501;

  public function __construct(string $reason, Throwable $prev = null)
  {
    TelegramLog::error("Server error: {$reason}\n");

    if ($_ENV["BOT_CREATOR_LINK"] ?? false) {
      $reason .= "\nPlease contact creator {$_ENV["BOT_CREATOR_LINK"]}";
    }

    parent::__construct($reason, self::CODE, $prev);
  }
}