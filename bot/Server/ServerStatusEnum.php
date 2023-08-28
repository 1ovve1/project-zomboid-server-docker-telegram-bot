<?php declare(strict_types=1);

namespace PZBot\Server;


enum ServerStatusEnum: string
{
  case ACTIVE = "ACTIVE";
  case RESTART = "RESTART";
  case DOWN = "DOWN";
  case PENDING = "PENDING";
  case UNDEFINED = "UNDEFINED";

  function withSmile(): string
  {
    return match($this) {
      self::ACTIVE => "ACTIVE 🍆",
      self::RESTART => "RESTART 🔃",
      self::DOWN => "DOWN ☠",
      self::PENDING => "PENDING ⏰",
      default => "UNDEFINED 🤡",
    };
  }
}