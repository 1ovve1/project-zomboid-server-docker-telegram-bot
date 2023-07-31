<?php declare(strict_types=1);

namespace PZBot\Server\Commands;

Enum CommandListEnum: string
{
  case SERVER_UP = "up server";
  case SERVER_DOWN = "down server";
  case SERVER_RESTART = "restart server";
  case SERVER_STATUS = "check if docker up";
  case GAME_LOGS_STATUS = "check if server running by logs information";
  case GAME_LOGS_DELETE = "delete logs";

  function exitCode() {
    return match($this) {
      self::SERVER_UP => "1",
      self::SERVER_DOWN => "2",
      self::SERVER_RESTART => "3",
      self::GAME_LOGS_DELETE => "6",
      default => "9999"
    };
  }
}