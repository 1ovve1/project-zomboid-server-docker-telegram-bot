<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser\Server;

use PZBot\Server\ServerStatusEnum;
use PZBot\Service\LogsParser\AbstractParser;
use PZBot\Service\LogsParser\DTO\ServerStatusObject;
use PZBot\Service\LogsParser\DTO\UniqueDTOInterface;

class ServerStartParser extends AbstractParser
{
  function getFilePath(): string
  {
    return 'data/Logs/*_DebugLog-server.txt';
  }

  function getRegExp(): string
  {
    return '/\*+ SERVER STARTED \*+\./';
  }

  function matchHandler(array $matches): UniqueDTOInterface
  {
    return new ServerStatusObject(ServerStatusEnum::ACTIVE);
  }
}