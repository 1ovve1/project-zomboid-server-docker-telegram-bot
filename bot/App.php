<?php declare(strict_types=1);

namespace PZBot;
use DateTime;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\TelegramLog;
use PZBot\Events\Emmiter;
use PZBot\Events\EmmiterFactoryInterface;
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
      try {
        $this->telegramUpdateCycle();

        $this->shedulerTasks();
      } catch (Throwable $e) {
        TelegramLog::error($e->getMessage(), [$e]);
      }

      sleep($sleep);
    }
  }

  /**
   * @return void
   * @throws Throwable
   */
  private function telegramUpdateCycle(): void
  {
    $this->emmiter->emmit(EventsEnum::BEFORE_HANDLE_UPDATES);

    $response = $this->handleUpdates();
  
    $this->emmiter->emmit(EventsEnum::AFTER_HANDLE_UPDATES);

    $this->handleResponse($response);

    $this->emmiter->emmit(EventsEnum::AFTER_HANDLE_RESPONSE);
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

  private function shedulerTasks(): void
  {
    $this->emmiter->emmit(
      EventsEnum::SHEDULER, 
      [
        "message" => "с добрым утром",
        "time" => (new DateTime())->setTime(09, 00),
      ],
      [
        "message" => "доброй ночи",
        "time" => (new DateTime())->setTime(23, 00),
      ]
    );

  }

}

