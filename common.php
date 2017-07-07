<?php

/**
 * shared by all "executable" php scripts in this project
 *
 */

//------------------------------------------------------------------------------
//CLASS AUTOLOADER

if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require __DIR__ . '/vendor/autoload.php';
}

//------------------------------------------------------------------------------
// CONFIG

$confFile = __DIR__ . "/conf/config.php";
require_once $confFile;
$localConf = __DIR__ . '/conf/config.local.php';
if (file_exists($localConf)) {
    require_once $localConf;
}

if (!isset($config)) {
    throw new \Exception("\$config variable not defined in [$confFile]");
}
