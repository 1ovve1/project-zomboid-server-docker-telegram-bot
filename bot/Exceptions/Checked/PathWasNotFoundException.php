<?php declare(strict_types=1);


namespace PZBot\Exceptions\Checked;

use Longman\TelegramBot\TelegramLog;
use PZBot\Exceptions\CheckedException;

class PathWasNotFoundException extends CheckedException
{
  const CODE = 404;

  public function __construct(string $path)
  {
    TelegramLog::warning("Path was not found: '{$path}'");

    parent::__construct($path, self::CODE, null);
  }
}