<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser;
use Generator;
use PZBot\Exceptions\Checked\LogsFilePremissionDeniedException;
use PZBot\Exceptions\Checked\LogsFileWasNotFoundedException;


class File
{
  readonly string $filePath;
  readonly Generator $data;

  /**
   * Load file by regex filepath
   * Then user $instance->data for data
   *
   * @param string $filePath
   * @throws LogsFilePremissionDeniedException
   * @throws LogsFileWasNotFoundedException
   */
  function __construct(string $filePath) 
  {
    $this->filePath = $this->resolveFilePath($filePath);
    $this->data = $this->open();
  }

  /**
   * Resolve file path with regex syntax
   *
   * @param string $filePath
   * @return string
   * @throws LogsFileWasNotFoundedException
   */
  private function resolveFilePath(string $filePath): string 
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
   * @return Generator
   * @throws LogsFilePremissionDeniedException
   */
  function open(): Generator
  {
      $file = fopen($this->filePath, 'r');

      if (is_bool($file)) {
        throw new LogsFilePremissionDeniedException($this->filePath);
      }

      while (($line = fgets($file)) !== false) {
        yield $line;
      }

      fclose($file);
  }

  /**
   * Open the file data
   *
   * @return Generator
   * @throws LogsFilePremissionDeniedException
   */
  function openReverse(): Generator
  {
    $file = fopen($this->filePath, 'r');

    if (is_bool($file)) {
      throw new LogsFilePremissionDeniedException($this->filePath);
    }

    $buffer = '';
    for($x_pos = 0; fseek($file, $x_pos, SEEK_END) !== -1; $x_pos--) {
      $char = fgetc($file);

      if ($char !== PHP_EOL) {
        $buffer = $char . $buffer;
      } else {
        yield $buffer;
        $buffer = '';
      }
    }

    fclose($file);
  }
}