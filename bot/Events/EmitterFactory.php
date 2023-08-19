<?php declare(strict_types=1);

namespace PZBot\Events;
use DateInterval;
use DateTimeImmutable;
use PZBot\Events\Handlers\ChatGptSylphietteMessageHandler;
use PZBot\Events\Handlers\ChatGptTsundereMessageHandler;
use PZBot\Events\Handlers\HandlersCollection;
use PZBot\Events\Handlers\RecreateAppHandler;
use PZBot\Events\Handlers\ServerStatusHandler;
use PZBot\Events\Handlers\TimerHandler;
use PZBot\Service\ImageResolver;
use PZBot\Service\LogsParser\LogsParserFactory;
use PZBot\Service\OpenAI\ChatGpt;

class EmitterFactory implements EmitterFactoryInterface
{

  function getEmitter(): Emmiter
  {
    $eventsCollection = new EventsCollection;

    $eventsCollection->addEventListener(
      EventsEnum::BEFORE_HANDLE_UPDATES,
      new TimerHandler(
        new RecreateAppHandler(),
        new DateTimeImmutable(),
        DateInterval::createFromDateString("8 hours"),
      )
    );

    $eventsCollection->addEventListener(
      EventsEnum::AFTER_HANDLE_RESPONSE,
      new ServerStatusHandler(
        new LogsParserFactory
      )
    );

    $eventsCollection->addEventListener(
      EventsEnum::SHEDULER,
      HandlersCollection::fromArray([
        
        'goodMorningMessage' => TimerHandler::fromString(
          new ChatGptTsundereMessageHandler(
            ImageResolver::fromEnv(),
            ChatGpt::fromEnv()
          ),
          env('BOT_CHATGPT_GOOD_MORNING_TIME'),
          "1 day"
        ),

        'goodNightMessage' => TimerHandler::fromString(
          new ChatGptSylphietteMessageHandler(
            ImageResolver::fromEnv(),
            ChatGpt::fromEnv()
          ),
          env('BOT_CHATGPT_GOOD_NIGHT_TIME'),
          "1 day"
        )
      ])
    );

    return new Emmiter($eventsCollection);
  }
}