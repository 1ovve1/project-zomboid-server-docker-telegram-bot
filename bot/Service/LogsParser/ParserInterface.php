<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser;
use PZBot\Service\LogsParser\DTO\UniqueDTOInterface;

interface ParserInterface
{
  /**
   * @return string
   */
  function getFilePath(): string;

  /**
   * @return string
   */
  function getRegExp(): string;

  /**
   * Main callback
   *
   * @param array<int, string> $matches
   * @return UniqueDTOInterface
   */
  function matchHandler(array $matches): UniqueDTOInterface;

  /**
   * Undocumented function
   *
   * @return array<int|string, UniqueDTOInterface>
   */
  function parse(): array;
}