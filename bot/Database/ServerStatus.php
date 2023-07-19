<?php declare(strict_types=1);

namespace PZBot\Database;

use PZBot\Server\Status;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;
use QueryBox\Migration\MigrateAble;
use QueryBox\QueryBuilder\QueryBuilder;

class ServerStatus extends QueryBuilder implements MigrateAble
{
  private static ?Status $lastStatus = null;

  protected static function getLastStatus(): Status 
  {
    if (self::$lastStatus === null) {
      self::setLastStatus(self::getStatus());
    }

    return self::$lastStatus;
  }

  protected static function setLastStatus(Status $status): void
  {
    self::$lastStatus = $status;
  }

  /**
   * @param Status $status
   * @return void
   */
  public static function updateStatus(Status $status): void
  {

    if (self::getLastStatus() !== $status) {
      TelegramLog::warning("Server status update on '{$status->value}'");
  
      Request::sendToActiveChats(
        'sendMessage',
        ["text" => "SERVER STATUS UPDATE: {$status->value}"],
        [
          'groups'      => true,
          'supergroups' => true,
          'channels'    => false,
          // 'users'       => true,
        ]
      );
  
      ServerStatus::insert([
        "status" => $status->value
      ])->save();

      self::setLastStatus($status);
    }
  }

  public static function getStatus(): Status
  {
    $queryResult = ServerStatus::select(["status"])->orderBy(["date"], false)->save();

    if ($queryResult->isNotEmpty()) {
      [["status" => $status]] = $queryResult->fetchAll();

      $statusEnum = Status::tryFrom($status);

      if ($statusEnum !== null) {

        return $statusEnum;
      }

    }

    return Status::UNDEFINED;
  }

  /**
   * @return boolean
   */
  public static function isRestarted(): bool
  {
      return ServerStatus::getLastStatus() === Status::RESTART;
  }

  /**
   * @return boolean
   */
  public static function isPending(): bool
  {
      return ServerStatus::getLastStatus() === Status::PENDIGN;
  }

  /**
   * @return boolean
   */
  public static function isDown(): bool
  {
      return ServerStatus::getLastStatus() === Status::DOWN;
  }

  /**
   * @return boolean
   */
  public static function isActive(): bool
  {
      return ServerStatus::getLastStatus() === Status::ACTIVE;
  }

  /**
   * @return boolean
   */
  public static function isUndefined(): bool
  {
      return ServerStatus::getLastStatus() === Status::UNDEFINED;
  }


  public static function migrationParams(): array
  {
    return [
      "fields" => [
        "id" => "BIGINT NOT NULL PRIMARY KEY AUTO_INCREMENT",
        "date" => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        "status" => "CHAR(10) NOT NULL",
      ]
    ];
  }
}