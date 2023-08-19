<?php declare(strict_types=1);

namespace PZBot;
use PZBot\Events\EmmiterFactory;
use PZBot\Telegram\TelegramCoreFactory;

class AppFactory
{
  protected Env $config;
  protected TelegramCoreFactory $telegramCoreFactory;

  function __construct(Env $config) {
    $this->config = $config;
    $this->telegramCoreFactory = new TelegramCoreFactory($config);
  }

  /**
   * Use ENV params for configuration and default emmiter
   *
   * @return App
   */
  function getApp(): App
  {
    return new App(
      $this->telegramCoreFactory->getProxy(),
      new EmmiterFactory($this->config)
    );
  }
}