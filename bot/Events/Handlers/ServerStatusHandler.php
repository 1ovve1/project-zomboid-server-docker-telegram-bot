<?php declare(strict_types=1);

namespace PZBot\Events\Handlers;

use PZBot\Database\ServerStatus;
use PZBot\Events\HandlerInterface;
use PZBot\Server\Commands\CommandListEnum;
use PZBot\Server\Commands\Factories\ExecutorFactoryInterface;
use PZBot\Server\StatusEnum;

class ServerStatusHandler implements HandlerInterface
{
  private ExecutorFactoryInterface $executorFactory;

  public function __construct(ExecutorFactoryInterface $executorFactory) 
  {
    $this->executorFactory = $executorFactory;
  }

  public function __invoke(mixed...$params): void
  {
    $executor = $this->executorFactory->getExecutor();


    if ($executor->execute(CommandListEnum::SERVER_STATUS)->isOK()) {

      if ($executor->execute(CommandListEnum::GAME_LOGS_STATUS)->isOK()) {
        ServerStatus::updateStatus(StatusEnum::ACTIVE);
      } else {
        if (!ServerStatus::isRestarted()) {
          ServerStatus::updateStatus(StatusEnum::PENDING);
        }
      }

    } else {
      ServerStatus::updateStatus(StatusEnum::DOWN);
    }
  }
}