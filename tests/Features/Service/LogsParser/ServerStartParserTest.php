<?php declare(strict_types=1);

namespace PZBot\Tests\Features\Service\LogsParser;
use PHPUnit\Framework\TestCase;
use PZBot\Server\ServerStatusEnum;
use PZBot\Service\LogsParser\DTO\ServerStatusObject;
use PZBot\Service\LogsParser\ParserInterface;
use PZBot\Tests\Mock\Service\LogsParser\MockLogsParserFactory;

class ServerStartParserTest extends TestCase
{
  protected ParserInterface $parser;

  function setUp(): void
  {
    $factory = new MockLogsParserFactory;
    $this->parser = $factory->getServerStatusParser();

    parent::setUp();
  }

  function testParse(): void
  {
    $expected = [
        new ServerStatusObject(
          ServerStatusEnum::ACTIVE,
        ),
    ];

    $this->assertEquals($expected, $this->parser->parse());
  }
}