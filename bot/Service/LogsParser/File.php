<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser;
use PZBot\Exceptions\Checked\LogsFilePremissionDeniedException;
use PZBot\Exceptions\Checked\LogsFileWasNotFoundedException;


class File
{
  readonly string $filePath;
  readonly array $data;

  /**
   * Load file by regex filepath
   * Then user $instance->data for data
   *
   * @param string $filePath
   * @throws LogsFileWasNotFoundedException
   * @throws LogsFilePremissionDeniedException
   */
  function __construct(string $filePath) 
  {
    $this->filePath = $this->resolveFilePath($filePath);
    $this->data = $this->open($this->filePath);
  }

  /**
   * Resolve file path with regex sintax
   *
   * @param string $filePath
   * @return string
   * @throws LogsFileWasNotFoundedException
   */
  protected static function resolveFilePath(string $filePath): string 
  {
    if (!glob($filePath)) {
      if (!glob(BASE_DIR . '/' . $filePath)) {
        throw new LogsFileWasNotFoundedException($filePath);
      } 
      
      return  BASE_DIR . '/' . $filePath;
    }

    return glob($filePath)[0];
  }

  /**
   * Open the file data
   *
   * @param string $filePath
   * @return array<string>
   * @throws LogsFilePremissionDeniedException
   */
  protected static function open(string $filePath): array
  {
    $file = file($filePath, FILE_IGNORE_NEW_LINES);

    if (is_bool($file)) {
      throw new LogsFilePremissionDeniedException($filePath);
    }

    return $file;
  }
}