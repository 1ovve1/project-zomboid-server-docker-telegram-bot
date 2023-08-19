<?php declare(strict_types=1);

namespace PZBot;
use Longman\TelegramBot\Exception\TelegramException;
use PZBot\Events\EmitterFactory;
use PZBot\Events\EmitterFactoryInterface;
use PZBot\Telegram\TelegramCoreFactory;

class AppFactory
{
  protected TelegramCoreFactory $telegramCoreFactory;
  protected EmitterFactoryInterface $emitterFactory;

  function __construct() {
    $this->telegramCoreFactory = new TelegramCoreFactory();
    $this->emitterFactory = new EmitterFactory();
  }

  /**
   * Use ENV params for configuration and default emitter
   *
   * @return App
   * @throws TelegramException
   */
  function getApp(): App
  {
    return new App(
      $this->telegramCoreFactory->getProxy(),
      $this->emitterFactory->getEmitter()
    );
  }
}