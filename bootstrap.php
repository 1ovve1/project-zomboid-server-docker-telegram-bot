<?php declare(strict_types=1);

require __DIR__ . "/vendor/autoload.php";

use Dotenv\Dotenv;
use Longman\TelegramBot\TelegramLog;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

/**
 * Global constants
 */
const BASE_DIR = __DIR__;
const BOT_DIR = __DIR__ . '/bot';

/**
 * Load .env file
 */
$dotenv = Dotenv::createImmutable(BASE_DIR);
$dotenv->load();

/**
 * Additional config params
 */
$_ENV["BOT_ADMIN_IDS"] = explode(',', $_ENV["BOT_ADMIN_IDS"]);

/**
 * Initialize logger instance
 */
$logLevel = match($_ENV["BOT_LOG_LEVEL"] ?? null) {
  "debug" => Level::Debug, 
  "error" => Level::Error,
  "warning" => Level::Warning, 
  default => Level::Info,
};
$logPath = BASE_DIR . $_ENV["BOT_LOG_PATH"] . "/pzbot.log";

TelegramLog::initialize(
  new Logger("pzbot", [
    (new RotatingFileHandler($logPath, 5, $logLevel))
      ->setFormatter(new LineFormatter(null, null, false, true)),
      
    (new StreamHandler('php://stdout', $logLevel))
      ->setFormatter(new LineFormatter(null, null, false, true)),
  ])
);