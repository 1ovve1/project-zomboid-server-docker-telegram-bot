<?php declare(strict_types=1);

namespace PZBot;
use Longman\TelegramBot\Telegram;

class TelegramCore extends Telegram implements TelegramCoreInterface
{
  protected Env $config;

  function __construct(Env $config)
  {
    $this->config = $config;

    $botApiKey = $config->get("BOT_API_KEY");
    $botUsername = $config->get("BOT_USERNAME");
    $commandsPath = $config->get("BOT_COMMANDS_PATH", "bot/CustomCommads");
    $useDb = $config->get("USE_DB", false);
    $dbConn = [
      'host'     => $config->get("DB_HOST"),
      'user'     => $config->get("DB_USER"),
      'password' => $config->get("DB_PASS"),
      'database' => $config->get("DB_NAME"),
    ];

    $this->addCommandsPaths([BASE_DIR . $commandsPath]);

    if ($useDb) {
      $this->enableMySql($dbConn);
    } else {
      $this->useGetUpdatesWithoutDatabase();
    }

    parent::__construct(
      $botApiKey, $botUsername
    );

  }
}