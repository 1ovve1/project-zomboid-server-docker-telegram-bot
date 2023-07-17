#!/usr/bin/env php
<?php declare(strict_types=1);

use Longman\TelegramBot\TelegramLog;

require_once __DIR__ . '/bot/bootstrap.php';

try {
    // Create Telegram API object
    $telegram = new Longman\TelegramBot\Telegram(
        $_ENV["BOT_API_KEY"], $_ENV["BOT_USERNAME"]
    );

    $telegram->addCommandsPaths($_ENV["CONFIG"]["commands"]["paths"]);
    $telegram->enableMySql([
        'host'     => $_ENV["DB_HOST"],
        'user'     => $_ENV["DB_USER"],
        'password' => $_ENV["DB_PASS"],
        'database' => $_ENV["DB_NAME"],
    ]);
    
    // Handle telegram getUpdates request
    $server_response = $telegram->handleGetUpdates();

    if ($server_response->isOk()) {
        $update_count = count($server_response->getResult());
        TelegramLog::info("Processed {$update_count} updates");
    } else {
        TelegramLog::error($server_response->printError());
    }

    require BOT_DIR . '/notifier.php';
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    Longman\TelegramBot\TelegramLog::error($e);
} catch (Longman\TelegramBot\Exception\TelegramLogException $e) {
    throw new RuntimeException($e->getMessage(), $e->getCode(), $e);
}
