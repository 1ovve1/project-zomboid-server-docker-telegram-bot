<?php declare(strict_types=1);

namespace PZBot\Database;

use Longman\TelegramBot\TelegramLog;
use PZBot\Helpers\TelegramRequestHelper;
use PZBot\Server\ServerStatusEnum;

class ServerStatus
{
  private static ?ServerStatusEnum $lastStatus = null;

  public static function getLastStatus(): ServerStatusEnum
  {
    if (self::$lastStatus === null) {
      self::$lastStatus = ServerStatusEnum::ACTIVE;
    }

    return self::$lastStatus;
  }

  protected static function setLastStatus(ServerStatusEnum $status): void
  {
    self::$lastStatus = $status;
  }

  /**
   * @param ServerStatusEnum $status
   * @return void
   */
  public static function updateStatus(ServerStatusEnum $status): void
  {

    if (self::getLastStatus() !== $status) {
      TelegramLog::warning("Server status update on '{$status->value}'");
      
      TelegramRequestHelper::sendMessageToAllGroups("Server status update... {$status->withSmile()}");

      self::setLastStatus($status);
    }
  }

  /**
   * @return boolean
   */
  public static function isRestarted(): bool
  {
      return ServerStatus::getLastStatus() === ServerStatusEnum::RESTART;
  }

  /**
   * @return boolean
   */
  public static function isPending(): bool
  {
      return ServerStatus::getLastStatus() === ServerStatusEnum::PENDING;
  }

  /**
   * @return boolean
   */
  public static function isDown(): bool
  {
      return ServerStatus::getLastStatus() === ServerStatusEnum::DOWN;
  }

  /**
   * @return boolean
   */
  public static function isActive(): bool
  {
      return ServerStatus::getLastStatus() === ServerStatusEnum::ACTIVE;
  }

  /**
   * @return boolean
   */
  public static function isUndefined(): bool
  {
      return ServerStatus::getLastStatus() === ServerStatusEnum::UNDEFINED;
  }

}