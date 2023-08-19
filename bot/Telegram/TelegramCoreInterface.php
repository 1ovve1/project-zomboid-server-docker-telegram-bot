<?php declare(strict_types=1);

namespace PZBot\Telegram;
use Longman\TelegramBot\Entities\ServerResponse;
use PZBot\Env;

interface TelegramCoreInterface
{
  public function enableMySql(array $credentials, string $table_prefix = '', string $encoding = 'utf8mb4');
  public function handleGetUpdates($data = null, ?int $timeout = null): ServerResponse;
  public function useGetUpdatesWithoutDatabase(bool $enable = true);
  public function getConfig(): Env;
}