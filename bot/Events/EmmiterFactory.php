<?php declare(strict_types=1);

namespace PZBot\Events;
use PZBot\Env;
use PZBot\Events\Handlers\ChatGptTsundereMessageHandler;
use PZBot\Events\Handlers\ServerStatusHandler;
use PZBot\Service\LogsParser\LogsParserFactory;

class EmmiterFactory implements EmmiterFactoryInterface
{
  function getEmmiter(): Emmiter
  {
    $eventsCollection = new EventsCollection;

    $eventsCollection->addEventListener(
      EventsEnum::AFTER_HANDLE_RESPONSE,
      new ServerStatusHandler(
        new LogsParserFactory
      )
    );

    $eventsCollection->addEventListener(
      EventsEnum::SHEDULER,
      new ChatGptTsundereMessageHandler(
        new Env
      )
    );

    return new Emmiter($eventsCollection);
  }
}