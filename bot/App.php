<?php declare(strict_types=1);

namespace PZBot;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\TelegramLog;
use PZBot\Events\Emmiter;
use PZBot\Events\EventsEnum;
use PZBot\Telegram\TelegramCoreInterface;
use Throwable;

class App
{
  /**
   * @var TelegramCoreInterface $code
   */
  readonly TelegramCoreInterface $core;
  /**
   * @var Emmiter $emitter
   */
  readonly Emmiter $emitter;

  /**
   * @param TelegramCoreInterface $core - telegram core
   * @param Emmiter $emitter
   */
  public function __construct(TelegramCoreInterface $core, Emmiter $emitter)
  {
    $this->core = $core;
    $this->emitter = $emitter;
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
    $this->emitter->emmit(EventsEnum::BEFORE_HANDLE_UPDATES, $this->core);

    $response = $this->handleUpdates();
  
    $this->emitter->emmit(EventsEnum::AFTER_HANDLE_UPDATES);

    $this->handleResponse($response);

    $this->emitter->emmit(EventsEnum::AFTER_HANDLE_RESPONSE);
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
   * Task in sheduler mode
   *
   * @return void
   */
  private function shedulerTasks(): void
  {
    $this->emitter->emmit(
      EventsEnum::SHEDULER, 
      ['goodMorningMessage' => "доброе утро"],
      ['goodNightMessage' => "доброй ночи"],
    );

  }

}

