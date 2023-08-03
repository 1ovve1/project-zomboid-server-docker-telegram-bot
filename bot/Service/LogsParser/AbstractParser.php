<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser;

use PZBot\Exceptions\Checked\LogsFilePremissionDeniedException;
use PZBot\Exceptions\Checked\LogsFileWasNotFoundedException;

abstract class AbstractParser implements ParserInterface
{
  private string $filePath;
  protected array $file;

  function __construct()
  {
    $this->filePath = $this->getFilePath();

    $this->checkFileExists();

    $this->file = array_reverse($this->open());
  }

  function parse(): array
  {
    $collection = [];

    foreach ($this->file as $line) {
      preg_match($this->getRegExp(), $line, $mathces);

      if (empty($mathces)) {
        continue;
      }

      $dto = $this->matchHandler($mathces);

      $key = $dto->getId();

      if (!isset($collection[$key])) {
        $collection[$key] = $dto;
      }
    }

    return $collection;
  }

  private function checkFileExists(): void
  {
    if (!glob($this->filePath)) {
      if (!glob(BASE_DIR . '/' . $this->filePath)) {
        throw new LogsFileWasNotFoundedException($this->filePath);
      } else {
        $this->filePath = BASE_DIR . '/' . $this->filePath;
      }
    }

    $this->filePath = glob($this->filePath)[0];
  }

  private function open() 
  {
    $file = file($this->filePath, FILE_IGNORE_NEW_LINES);

    if (is_bool($file)) {
      throw new LogsFilePremissionDeniedException($this->filePath);
    }

    return $file;
  }
}