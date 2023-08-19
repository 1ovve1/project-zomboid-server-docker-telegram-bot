<?php declare(strict_types=1);

namespace PZBot\Telegram;

class TelegramCoreFactory
{
  /**
   * @return TelegramCoreInterface
   * @throws \Longman\TelegramBot\Exception\TelegramException
   */
  function getCore(): TelegramCoreInterface
  {
    return TelegramCore::fromEnv();
  }

  /**
   * @return TelegramCoreInterface
   * @throws \Longman\TelegramBot\Exception\TelegramException
   */
  function getProxy(): TelegramCoreInterface
  {
    return TelegramCoreProxy::fromEnv();
  }
}