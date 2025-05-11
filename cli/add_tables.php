<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    (new \App\Bootstrap\Cli($argv))->process();
} catch (Exception $e) {
    echo $e->getMessage();
}
