<?php declare(strict_types=1);

namespace PZBot\Exceptions\Checked;

use PZBot\Exceptions\CheckedException;

class LogsFileWasNotFoundedException extends CheckedException
{
  const MESSAGE = "File was not founded by path: %s";

  public function __construct(string $filePath) {
    parent::__construct(
      sprintf(self::MESSAGE, $filePath),
    );
  }
}