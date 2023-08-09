<?php declare(strict_types=1);

namespace PZBot;
use PZBot\Events\EmmiterFactory;

class AppFactory
{
  protected Env $config;

  function __construct(Env $config) {
    $this->config = $config;
  }

  /**
   * Use ENV params for configuration and default emmiter
   *
   * @return App
   */
  function getApp(): App
  {
    return new App(
      new TelegramCore(new Env()),
      new EmmiterFactory($this->config)
    );
  }
}