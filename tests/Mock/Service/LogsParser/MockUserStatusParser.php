<?php declare(strict_types=1);

namespace PZBot\Tests\Mock\Service\LogsParser;
use PZBot\Service\LogsParser\User\UserStatusParser;

class MockUserStatusParser extends UserStatusParser
{
  function getFilePath(): string
  {
    return BASE_DIR . '/tests/data/*_user.txt';
  }
}