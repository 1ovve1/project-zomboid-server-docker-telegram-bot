<?php declare(strict_types=1);

namespace PZBot;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\TelegramLog;
use PZBot\Events\Emmiter;
use PZBot\Events\EmmiterFactoryInterface;
use PZBot\Events\EventsCollection;
use PZBot\Events\EventsEnum;
use Throwable;

class App
{
  readonly TelegramCoreInterface $core;
  readonly Emmiter $emmiter;

  public function __construct(TelegramCoreInterface $core, EmmiterFactoryInterface $emmiterFactory) 
  {
    $this->core = $core;
    $this->emmiter = $emmiterFactory->getEmmiter();
  }

  /**
   * Main cron method

   * @param integer $sleep
   * @return never
   */
  public function cron(int $sleep = 1): never
  {
    while(true) {
      $this->emmiter->emmit(EventsEnum::BEFORE_HANDLE_UPDATES);

      try {
        $response = $this->handleUpdates();
  
        $this->emmiter->emmit(EventsEnum::AFTER_HANDLE_UPDATES);
  
        $this->handleResponse($response);
  
        $this->emmiter->emmit(EventsEnum::AFTER_HANDLE_RESPONSE);
      } catch (Throwable $e) {
        TelegramLog::error($e->getMessage(), [$e]);
      }

      sleep($sleep);
    }
  }

  /**
   * @return ServerResponse
   */
  private function handleUpdates(): ServerResponse
  {
    return $this->core->handleGetUpdates();
  }

  /**
   * @param ServerResponse $response
   * @return void
   */
  private function handleResponse(ServerResponse $response): void
  {
    if ($response->isOk()) {
      $update_count = count($response->getResult());
      TelegramLog::info("Processed {$update_count} updates");
    } else {
      TelegramLog::error($response->printError());
    }
  }

  /**
   * Add events
   *
   * @param EventsCollection $events
   * @return void
   */
  function useEvents(EventsCollection $events): void
  {
    $this->emmiter->useCollection($events);
  }

}

