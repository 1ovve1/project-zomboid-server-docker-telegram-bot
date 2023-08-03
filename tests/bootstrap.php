<?php declare(strict_types=1);

use Dotenv\Dotenv;

/**
 * Set default timezon
 */
date_default_timezone_set('Etc/GMT-3');

/**
 * Global constants
 */
const BASE_DIR = __DIR__ . '/../';
const BOT_DIR = BASE_DIR. '/bot';

/**
 * Load .env file
 */
$dotenv = Dotenv::createImmutable(BASE_DIR);
$dotenv->load();

/**
 * Additional config params
 */
$_ENV["BOT_ADMIN_IDS"] = explode(',', $_ENV["BOT_ADMIN_IDS"]);
foreach ($_ENV as $name => &$param) {
  if (str_contains($name, "PATH")) {
    $param = BASE_DIR . '/' . $param;
  }
}
