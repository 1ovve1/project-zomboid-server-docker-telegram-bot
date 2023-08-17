<?php declare(strict_types=1);

namespace PZBot\Telegram;
use PZBot\Env;

class TelegramCoreFactory
{
  protected Env $config;

  public function __construct(?Env $config = null) {
    $this->config = $config ?? new Env;
  }

  function getCore(): TelegramCoreInterface
  {
    return new TelegramCore($this->config);
  }

  function getProxy(): TelegramCoreInterface
  {
    return new TelegramCoreProxy($this);
  }
}