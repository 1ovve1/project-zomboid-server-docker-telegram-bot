<?php declare(strict_types=1);

namespace PZBot\Database;

use Longman\TelegramBot\Request;
use Longman\TelegramBot\TelegramLog;
use PZBot\Helpers\TelegramRequestHelper;
use PZBot\Server\StatusEnum;

class ServerStatus
{
  private static ?StatusEnum $lastStatus = null;

  public static function getLastStatus(): StatusEnum
  {
    if (self::$lastStatus === null) {
      self::$lastStatus = StatusEnum::UNDEFINED;
    }

    return self::$lastStatus;
  }

  protected static function setLastStatus(StatusEnum $status): void
  {
    self::$lastStatus = $status;
  }

  /**
   * @param StatusEnum $status
   * @return void
   */
  public static function updateStatus(StatusEnum $status): void
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
      return ServerStatus::getLastStatus() === StatusEnum::RESTART;
  }

  /**
   * @return boolean
   */
  public static function isPending(): bool
  {
      return ServerStatus::getLastStatus() === StatusEnum::PENDING;
  }

  /**
   * @return boolean
   */
  public static function isDown(): bool
  {
      return ServerStatus::getLastStatus() === StatusEnum::DOWN;
  }

  /**
   * @return boolean
   */
  public static function isActive(): bool
  {
      return ServerStatus::getLastStatus() === StatusEnum::ACTIVE;
  }

  /**
   * @return boolean
   */
  public static function isUndefined(): bool
  {
      return ServerStatus::getLastStatus() === StatusEnum::UNDEFINED;
  }

}