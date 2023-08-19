<?php declare(strict_types=1);

namespace PZBot\Telegram;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\TelegramException;

class TelegramCoreProxy implements TelegramCoreInterface, TelegramCoreProxyInterface
{
  protected TelegramCoreInterface $coreInstance;

  private string $botApiKey;
  private string $botUsername;
  private string $commandsPath;
  private bool $useDb = false;
  private array $dbConnectionParams = [];

  /**
   * @return self
   * @throws TelegramException
   */
  static function fromEnv(): self
  {
    return new self(
        env("BOT_API_KEY"),
        env("BOT_USERNAME"),
        env("BOT_COMMANDS_PATH", __DIR__ . "/CustomCommands"),
        (bool)env("BOT_USE_DB", false),
        [
            'host'     => env("DB_HOST", 'localhost'),
            'user'     => env("DB_USER", ''),
            'password' => env("DB_PASS", ''),
            'database' => env("DB_NAME", ''),
        ],
    );
  }

  /**
   * @param string $botApiKey
   * @param string $botUsername
   * @param string $commandsPath
   * @param bool $useDb
   * @param array $dbConnectionParams
   * @throws TelegramException
   */
  public function __construct(string $botApiKey,
                              string $botUsername,
                              string $commandsPath,
                              bool $useDb = false,
                              array $dbConnectionParams = [])
  {
    $this->botApiKey = $botApiKey;
    $this->botUsername = $botUsername;
    $this->commandsPath = $commandsPath;
    $this->useDb = $useDb;
    $this->dbConnectionParams = $dbConnectionParams;

    $this->coreInstance = $this->createTelegramCoreInstance();
  }

  /**
   * @return TelegramCoreInterface
   * @throws TelegramException
   */
  function createTelegramCoreInstance(): TelegramCoreInterface
  {
    return new TelegramCore(
        $this->botApiKey,
        $this->botUsername,
        $this->commandsPath,
        $this->useDb,
        $this->dbConnectionParams
    );
  }

  /**
   * @param array $credentials
   * @param string $table_prefix
   * @param string $encoding
   * @return void
   */
  public function enableMySql(array $credentials, string $table_prefix = '', string $encoding = 'utf8mb4'): void
  {
    $this->coreInstance->enableMySql($credentials, $table_prefix, $encoding);
  }

  /**
   * @param $data
   * @param int|null $timeout
   * @return ServerResponse
   */
  public function handleGetUpdates($data = null, ?int $timeout = null): ServerResponse
  {
    return $this->coreInstance->handleGetUpdates($data, $timeout);
  }

  /**
   * @param bool $enable
   * @return void
   */
  public function useGetUpdatesWithoutDatabase(bool $enable = true): void
  {
    $this->useGetUpdatesWithoutDatabase($enable);
  }

  /**
   * @return void
   * @throws TelegramException
   */
  public function recreate(): void
  {
    unset($this->coreInstance);
    $this->coreInstance = $this->createTelegramCoreInstance();
  }
}