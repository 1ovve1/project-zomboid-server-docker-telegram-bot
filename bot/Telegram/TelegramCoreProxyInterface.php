<?php declare(strict_types=1);

namespace PZBot\Telegram;

interface TelegramCoreProxyInterface
{
  function recreate(): void;
}