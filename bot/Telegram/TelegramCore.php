<?php declare(strict_types=1);

namespace PZBot\Telegram;
use Longman\TelegramBot\Telegram;

/**
 * Telegram wrapper
 */
class TelegramCore extends Telegram implements TelegramCoreInterface
{
  /**
   * Create telegram core instance
   *
   * @param string $botApiKey
   * @param string $botUsername
   * @param string $commandsPath - path for bot commands
   * @param bool $useDb - flag for mysql db usage
   * @param array{
   *   host?: string,
   *   user: string,
   *   password: string,
   *   database: string
   * } $dbConnectionParams - db connection params
   * @throws \Longman\TelegramBot\Exception\TelegramException
   */
  function __construct(string $botApiKey,
                       string $botUsername,
                       string $commandsPath,
                       bool $useDb = false,
                       array $dbConnectionParams = [])
  {
    $this->addCommandsPaths([$commandsPath]);

    if ($useDb) {
      $this->enableMySql($dbConnectionParams);
    } else {
      $this->useGetUpdatesWithoutDatabase();
    }

    parent::__construct(
      $botApiKey, $botUsername
    );

  }

  /**
   * Create telegram core instance use $_ENV params from .env
   *
   * @return self
   * @throws \Longman\TelegramBot\Exception\TelegramException
   */
  public static function fromEnv(): self
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
}