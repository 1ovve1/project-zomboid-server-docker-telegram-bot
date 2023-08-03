<?php declare(strict_types=1);

namespace PZBot\Exceptions\Checked;
use PZBot\Exceptions\CheckedException;

class LogsFilePremissionDeniedException extends CheckedException
{
  const MESSAGE = "Premission denied to open file: %s";

  public function __construct(string $fileName) 
  {
    parent::__construct(
      sprintf(self::MESSAGE, $fileName)
    );
  }
}