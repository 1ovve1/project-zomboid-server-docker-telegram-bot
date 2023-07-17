<?php declare(strict_types=1);

namespace PZBot\Database;

use Longman\TelegramBot\TelegramLog;
use PZBot\Server\Status;
use QueryBox\Migration\MigrateAble;
use QueryBox\QueryBuilder\QueryBuilder;

class ServerStatus extends QueryBuilder implements MigrateAble
{
  /**
   * @param Status $status
   * @return void
   */
  public static function updateStatus(Status $status): void
  {
    TelegramLog::warning("Server status update on '{$status->value}'");

    ServerStatus::insert([
      "status" => $status->value
    ])->save();
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
      return ServerStatus::getStatus() === Status::RESTART;
  }

  /**
   * @return boolean
   */
  public static function isPending(): bool
  {
      return ServerStatus::getStatus() === Status::PENDIGN;
  }

  /**
   * @return boolean
   */
  public static function isDown(): bool
  {
      return ServerStatus::getStatus() === Status::DOWN;
  }

  /**
   * @return boolean
   */
  public static function isActive(): bool
  {
      return ServerStatus::getStatus() === Status::ACTIVE;
  }

  /**
   * @return boolean
   */
  public static function isUndefined(): bool
  {
      return ServerStatus::getStatus() === Status::UNDEFINED;
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