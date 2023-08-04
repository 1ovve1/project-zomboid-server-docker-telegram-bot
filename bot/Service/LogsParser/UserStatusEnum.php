<?php declare(strict_types=1);

namespace PZBot\Service\LogsParser;
use PZBot\Helpers\DateTimeHelper;

enum UserStatusEnum: string
{
  case ATTEMPTING = 'attempting to join\.';
  case ALLOWED = 'allowed to join\.';
  case IN_QUEUE = 'attempting to join used queue\.';
  case CONNECTED = 'fully connected \(([\d,]+)\)\.';
  case CONNECTION_LOST = 'removed connection index=(\d+)\.';
  case DISCONNECTED = 'disconnected player \(([\d,]+)\)\.';
  case UNDEFINED = 'undefined';

  static function find(string $raw): self
  {
    foreach (self::cases() as $enum) {
      if ($enum->preg($raw) !== false) {

        return $enum;
      }
    }
    
    return self::UNDEFINED;
  }

  function preg(string $raw): array|false
  {
    preg_match('/' . $this->value . '/', $raw, $matches);

    return empty($matches) ? false: $matches;
  }

  function emoji(): string
  {
    return match($this) {
      self::ATTEMPTING , self::ALLOWED , self::IN_QUEUE, self::CONNECTION_LOST => '🔴',
      self::CONNECTED => '🔵',
      self::DISCONNECTED => '⚫',
      default => '⚪',
    };
  }
}