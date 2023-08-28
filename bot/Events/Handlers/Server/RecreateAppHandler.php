<?php declare(strict_types=1);

namespace PZBot\Events\Handlers\Server;
use PZBot\Events\HandlerInterface;
use PZBot\Telegram\TelegramCoreProxyInterface;
use QueryBox\DBFacade;

class RecreateAppHandler implements HandlerInterface
{
  function __invoke(mixed ...$params): void
  {
    $this->recreateTelegramCore($params);

    $this->recreateQueryBox();
  }

  /**
   * Serach telegram proxy instance in params and recreate that
   *
   * @param array $params
   * @return void
   */
  private function recreateTelegramCore(array $params): void
  {
    foreach($params as $value) {
      if ($value instanceof TelegramCoreProxyInterface) {
        $value->recreate();
      }
    }
  }

  private function recreateQueryBox(): void
  {
    DBFacade::setImmutable(false);
    DBFacade::getDBInstance();
    DBFacade::setImmutable(true);
  }
}
