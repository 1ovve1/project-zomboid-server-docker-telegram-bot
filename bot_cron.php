#!/usr/bin/env php
<?php declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

use PZBot\AppFactory;
use PZBot\Env;


$appFactory = new AppFactory(new Env());

$app = $appFactory->getApp();

$app->cron();