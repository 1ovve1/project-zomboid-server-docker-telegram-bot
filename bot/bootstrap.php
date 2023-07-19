<?php declare(strict_types=1);

require __DIR__ . "/../vendor/autoload.php";

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
const BASE_DIR = __DIR__ . "/..";
const BOT_DIR = __DIR__;

/**
 * Load .env file
 */
$dotenv = Dotenv::createImmutable(BASE_DIR);
$dotenv->load();

/**
 * Additional config params
 */
$_ENV["BOT_ADMIN_IDS"] = explode(',', $_ENV["BOT_ADMIN_IDS"]);
$_ENV["DB_TYPE"] = "mysql"; // fixed bd driver for pdo, need for QueryBox initialization
$_ENV["LOG_QUERY_RESULTS"] = false;
$_ENV["CONFIG"] = require BOT_DIR . "/config.php"; // additional config params

/**
 * Initialize logger instance
 */
$logLevel = match($_ENV["LOG_LEVEL"] ?? null) {
  "debug" => Level::Debug, "error" => Level::Error,
  "warning" => Level::Warning, default => Level::Info,
};
TelegramLog::initialize(
  new Logger("pzbot", [
    (new RotatingFileHandler(BASE_DIR . "/data/logs/pzbot.log", 5, $logLevel))->setFormatter(new LineFormatter(null, null, false, true)),
    (new StreamHandler('php://stdout', $logLevel))->setFormatter(new LineFormatter(null, null, false, true)),
  ])
);