#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/bot/bootstrap.php';

use PZBot\AppFactory;


$appFactory = new AppFactory;

$app = $appFactory->getApp();

$app->cron();