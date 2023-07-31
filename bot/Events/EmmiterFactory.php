<?php declare(strict_types=1);

namespace PZBot\Events;
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

    return new Emmiter($eventsCollection);
  }
}