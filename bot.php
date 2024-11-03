<?php

require_once './vendor/autoload.php';

App\Core\EnvHelper::parseDotEnv();
$telegramToken = getenv('TELEGRAM_TOKEN');

$botRunner = new App\Bot\BotRunner(
    $telegramToken,
    __DIR__ . '/config/general.php'
);
$botRunner->run();
