<?php declare(strict_types=1);

namespace PZBot\Telegram;
use Longman\TelegramBot\Entities\ServerResponse;
use PZBot\Env;
use PZBot\Telegram\TelegramCoreFactory;

class TelegramCoreProxy implements TelegramCoreInterface, TelegramCoreProxyInterface
{
  protected TelegramCoreFactory $coreFactory;
  protected TelegramCoreInterface $coreInstance;

  public function __construct(TelegramCoreFactory $coreFactory) {
    $this->coreFactory = $coreFactory;
    $this->coreInstance = $coreFactory->getCore();
  }

  public function enableMySql(array $credentials, string $table_prefix = '', string $encoding = 'utf8mb4')
  {
    $this->coreInstance->enableMySql($credentials, $table_prefix, $encoding);
  }

  public function handleGetUpdates($data = null, ?int $timeout = null): ServerResponse
  {
    return $this->coreInstance->handleGetUpdates($data, $timeout);
  }

  public function useGetUpdatesWithoutDatabase(bool $enable = true)
  {
    $this->useGetUpdatesWithoutDatabase($enable);
  }
  
  public function getConfig(): Env
  {
    return $this->coreInstance->getConfig();
  }

  public function recreate(): void
  {
    unset($this->coreInstance);
    $this->coreInstance = $this->coreFactory->getCore();
  }
}