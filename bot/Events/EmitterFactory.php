<?php declare(strict_types=1);

namespace PZBot\Events;
use DateInterval;
use DateTimeImmutable;
use PZBot\Events\Handlers\ChatGptMessages\ChatGptSylphietteMessageHandler;
use PZBot\Events\Handlers\ChatGptMessages\ChatGptTsundereMessageHandler;
use PZBot\Events\Handlers\Decorators\HandlersCollection;
use PZBot\Events\Handlers\Decorators\TimerHandler;
use PZBot\Events\Handlers\Server\RecreateAppHandler;
use PZBot\Events\Handlers\Server\ServerStatusHandler;
use PZBot\Server\Commands\Bash\BashCommandResolver;
use PZBot\Server\Commands\Factories\ExecutorFactory;
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
        new LogsParserFactory, new ExecutorFactory(new BashCommandResolver())
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