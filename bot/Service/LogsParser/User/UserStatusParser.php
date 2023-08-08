<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser\User;
use PZBot\Service\LogsParser\AbstractParser;
use PZBot\Service\LogsParser\DTO\UniqueDTOInterface;
use PZBot\Service\LogsParser\DTO\UserActivityObject;

class UserStatusParser extends AbstractParser
{
  /**
   * @inheritDoc
   */
  function getFilePath(): string
  {
    return BASE_DIR . "/data/Logs/*_user.txt";
  }

  /**
   * RegExp conatins:
   * 1. Time;
   * 2. ID (local pz id);
   * 3. Name;
   * 4. Action data (like disconnected, connected and other, look at UserStatusEnum for detail)
   *
   * @return string
   */
  function getRegExp(): string
  {
    return '/\[(.*?)\]\s(\d+)\s"(.*?)"\s(.*?\.)$/';
  }

  /**
   * @inheritDoc
   */
  function matchHandler(array $matches): UniqueDTOInterface
  {
    return UserActivityObject::fromStringArray([
      'time' => $matches[1],    // Time
      'id' => (int)$matches[2], // id
      'name' => $matches[3],    // username
      'action' => $matches[4],  // action
    ]);
  }
}