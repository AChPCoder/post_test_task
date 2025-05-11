<?php

namespace App\Helper;

class Config
{
    private static ?array $config = null;

    public static function getConfig()
    {
        if (!isset(self::$config)) {
            self::$config = include(__DIR__ . '/../../config/config.php');
        }
        return self::$config;
    }

}