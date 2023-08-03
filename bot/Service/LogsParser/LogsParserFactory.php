<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser;

class LogsParserFactory
{
  function getUserStatusParser(): ParserInterface
  {
    return new UserStatusParser;
  }
}