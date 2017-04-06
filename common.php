<?php

/**
 * shared by all "executable" php scripts in this project
 *
 */

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

$confFile = __DIR__ . "/conf/config.php";
require_once $confFile;

if (!isset($config)) {
    throw new \Exception("\$config variable not defined in [$confFile]");
}

