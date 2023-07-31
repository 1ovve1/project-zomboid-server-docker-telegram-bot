<?php declare(strict_types=1);

namespace PZBot;
use PZBot\Events\EmmiterFactory;

class AppFactory
{
  /**
   * Use ENV params for configuration and default emmiter
   *
   * @return App
   */
  static function getApp(): App
  {
    $emmiterFactory = new EmmiterFactory;

    return new App(
      new TelegramCore(new Env()),
      new EmmiterFactory()
    );
  }
}