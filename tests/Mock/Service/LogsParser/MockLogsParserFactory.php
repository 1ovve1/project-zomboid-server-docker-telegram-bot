<?php declare(strict_types=1);

namespace PZBot\Tests\Mock\Service\LogsParser;
use PZBot\Service\LogsParser\ParserInterface;
use PZBot\Service\LogsParser\ParserOptionsEnum;

class MockLogsParserFactory
{
  function getUserStatusParser(): ParserInterface
  {
    return MockUserStatusParser::create(
      ParserOptionsEnum::UNIQUE, ParserOptionsEnum::FROM_TOP
    )->setLimit(50);
  }

  function getServerStatusParser(): ParserInterface
  {
    return MockServerStartParser::create(
      ParserOptionsEnum::ONCE, ParserOptionsEnum::FROM_TOP
    )->setLimit(50);
  }
}