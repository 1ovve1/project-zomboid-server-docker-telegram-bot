<?php declare(strict_types=1);

namespace PZBot\Telegram;
use Longman\TelegramBot\Telegram;
use PZBot\Env;

class TelegramCore extends Telegram implements TelegramCoreInterface
{
  protected Env $config;

  function __construct(Env $config)
  {
    $this->config = $config;

    $botApiKey = $config->get("BOT_API_KEY");
    $botUsername = $config->get("BOT_USERNAME");
    $commandsPath = $config->get("BOT_COMMANDS_PATH", __DIR__ . "/CustomCommads");
    $useDb = $config->get("BOT_USE_DB", false);
    $dbConn = [
      'host'     => $config->get("DB_HOST"),
      'user'     => $config->get("DB_USER"),
      'password' => $config->get("DB_PASS"),
      'database' => $config->get("DB_NAME"),
    ];

    $this->addCommandsPaths([$commandsPath]);

    if ($useDb) {
      $this->enableMySql($dbConn);
    } else {
      $this->useGetUpdatesWithoutDatabase();
    }

    parent::__construct(
      $botApiKey, $botUsername
    );

  }

  public function getConfig(): Env
  {
    return $this->config;
  }
}