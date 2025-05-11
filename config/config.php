<?php

$config = [];
$config = include(__DIR__ . '/config.common.php');
if (file_exists(__DIR__ . '/config.common.php')) {
    $config_included = include(__DIR__ . '/config.common.php');
    $config = array_merge($config, $config_included);
}
if (file_exists(__DIR__ . '/config.local.php')) {
    $config_included = include(__DIR__ . '/config.local.php');
    $config = array_merge($config, $config_included);
}
return $config;