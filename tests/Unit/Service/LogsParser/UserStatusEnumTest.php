<?php declare(strict_types=1);

namespace PZBot\Tests\Unit\Service\LogsParser;
use PHPUnit\Framework\TestCase;
use PZBot\Service\LogsParser\User\UserStatusEnum;

class UserStatusEnumTest extends TestCase
{
  const EXPECTED_CASES = [
    [
      "expected" => UserStatusEnum::DISCONNECTED,
      "raw" => "disconnected player (7661,3480,0).",
    ],
    [
      "expected" => UserStatusEnum::CONNECTION_LOST,
      "raw" => "removed connection index=2.",
    ],
    [
      "expected" => UserStatusEnum::CONNECTED,
      "raw" => "fully connected (8941,6638,0).",
    ],
    [
      "expected" => UserStatusEnum::IN_QUEUE,
      "raw" => "attempting to join used queue.",
    ],
    [
      "expected" => UserStatusEnum::ALLOWED,
      "raw" => "allowed to join.",
    ],
    [
      "expected" => UserStatusEnum::ATTEMPTING,
      "raw" => "attempting to join.",
    ],
    [
      "expected" => UserStatusEnum::UNDEFINED,
      "raw" => "unknown"
    ],
  ];

  function testCases(): void
  {
    foreach (self::EXPECTED_CASES as ["expected" => $enumExpected, "raw" => $raw]) {
      $finded = UserStatusEnum::find($raw);
      $this->assertEquals($enumExpected, $finded);
    }
  }
}