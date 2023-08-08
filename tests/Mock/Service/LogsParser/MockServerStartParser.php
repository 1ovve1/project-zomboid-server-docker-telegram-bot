<?php declare(strict_types=1);

namespace PZBot\Tests\Mock\Service\LogsParser;
use PZBot\Service\LogsParser\Server\ServerStartParser;

class MockServerStartParser extends ServerStartParser
{
  function getFilePath(): string
  {
    return BASE_DIR . '/tests/data/*_DebugLog-server.txt';
  }
}