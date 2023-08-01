<?php declare(strict_types=1);

namespace PZBot\Events;
use PZBot\Env;
use PZBot\Events\Handlers\ChatGptTsundereMessageHandler;
use PZBot\Events\Handlers\ServerStatusHandler;
use PZBot\Server\Commands\Bash\BashCommandResolver;
use PZBot\Server\Commands\Factories\ExecutorFactory;

class EmmiterFactory implements EmmiterFactoryInterface
{
  function getEmmiter(): Emmiter
  {
    $eventsCollection = new EventsCollection;

    $eventsCollection->addEventListener(
      EventsEnum::AFTER_HANDLE_RESPONSE,
      new ServerStatusHandler(
        new ExecutorFactory(
          new BashCommandResolver(false)
        )
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