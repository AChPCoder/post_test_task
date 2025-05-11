<?php

require_once __DIR__ . '/../vendor/autoload.php';

$input_argv = $argv;

if (!isset($input_argv[1])) {
    $input_argv[1] = 'import_fetch';
}

try {
    (new \App\Bootstrap\Cli($input_argv))->process();
} catch (Exception $e) {
    echo $e->getMessage();
}
