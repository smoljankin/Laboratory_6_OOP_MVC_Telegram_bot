<?php

namespace App\Core;

class EnvHelper {
    public static function parseDotEnv() {
        $envFile = file_get_contents('.env');
        $envVars = explode("\n", $envFile);
        foreach ($envVars as $line) {
            list($key, $value) = explode('=', $line);
            putenv("$key=$value");
        }
    }
}
