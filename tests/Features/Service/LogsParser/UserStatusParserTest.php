<?php declare(strict_types=1);

namespace PZBot\Tests\Features\Service\LogsParser;
use DateTime;
use PHPUnit\Framework\TestCase;
use PZBot\Service\LogsParser\DTO\UserActivityObject;
use PZBot\Service\LogsParser\UserStatusEnum;
use PZBot\Tests\Mock\Service\LogsParser\MockUserStatusParser;

class UserStatusParserTest extends TestCase
{
  protected MockUserStatusParser $parser;

  function setUp(): void
  {
    $this->parser = new MockUserStatusParser;

    parent::setUp();
  }

  function testParse(): void
  {
    $expected = [
      76561198386872268 => 
        new UserActivityObject(
          76561198386872268,
          "UwU",
          UserStatusEnum::DISCONNECTED,
          DateTime::createFromFormat('d-m-y H:i:s.v', "03-08-23 00:19:25.753"),
        ),
      76561198144482447 => 
        new UserActivityObject(
          76561198144482447,
          "Warlord",
          UserStatusEnum::CONNECTION_LOST,
          DateTime::createFromFormat('d-m-y H:i:s.v', "03-08-23 00:16:04.522"),
        ),
      76561199118924867 => 
        new UserActivityObject(
          76561199118924867,
          "MomoWith",
          UserStatusEnum::CONNECTED,
          DateTime::createFromFormat('d-m-y H:i:s.v', "02-08-23 23:10:13.426"),
        ),
      76561198144482423 => 
        new UserActivityObject(
          76561198144482423,
          "Gegemon",
          UserStatusEnum::IN_QUEUE,
          DateTime::createFromFormat('d-m-y H:i:s.v', "02-08-23 19:54:26.039"),
        ),
      76561198080329390 => 
        new UserActivityObject(
          76561198080329390,
          "1owe1",
          UserStatusEnum::ALLOWED,
          DateTime::createFromFormat('d-m-y H:i:s.v', "02-08-23 19:54:38.120"),
        ),
      76561198386872212 => 
        new UserActivityObject(
          76561198386872212,
          "SoloWey",
          UserStatusEnum::ATTEMPTING,
          DateTime::createFromFormat('d-m-y H:i:s.v', "03-08-23 00:19:25.753"),
        ),
    ];

    $this->assertEquals($expected, $this->parser->parse());
  }
}