<?php declare(strict_types=1);

namespace PZBot\Events;
use PZBot\Env;
use PZBot\Events\Handlers\ChatGptSylphietteMessageHandler;
use PZBot\Events\Handlers\ChatGptTsundereMessageHandler;
use PZBot\Events\Handlers\HandlersCollection;
use PZBot\Events\Handlers\ServerStatusHandler;
use PZBot\Events\Handlers\TimerHandler;
use PZBot\Service\ImageResolver;
use PZBot\Service\LogsParser\LogsParserFactory;
use PZBot\Service\OpenAI\ChatGpt;

class EmmiterFactory implements EmmiterFactoryInterface
{
  protected Env $config;

  function __construct(Env $config) {
    $this->config = $config;
  }

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
      HandlersCollection::fromArray([
        
        'goodMorningMessage' => TimerHandler::fromString(
          new ChatGptTsundereMessageHandler(
            ImageResolver::fromEnv($this->config), 
            ChatGpt::fromEnv($this->config)
          ),
          $this->config->get('BOT_CHATGPT_GOOD_MORNING_TIME'),
          "1 day"
        ),

        'goodNightMessage' => TimerHandler::fromString(
          new ChatGptSylphietteMessageHandler(
            ImageResolver::fromEnv($this->config), 
            ChatGpt::fromEnv($this->config)
          ),
          $this->config->get('BOT_CHATGPT_GOOD_NIGHT_TIME'),
          "1 day"
        )
      ])
    );

    return new Emmiter($eventsCollection);
  }
}