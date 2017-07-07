<?php

$config = array(
    "dbConnection" => array(
        //if you can, use localhost instead of 127.0.0.1 to speed up access on Linux. see a comment in http://php.net/manual/en/pdo.connections.php
            "dsn" => "mysql:dbname=rate_limiter_test;host=localhost",
            "user" => "root",
            "pass" => "",
        ),

    //for testing purpose
    "dbConnectionMisconfiguration" => array(
        //if you can, use localhost instead of 127.0.0.1 to speed up access on Linux. see a comment in http://php.net/manual/en/pdo.connections.php
            "dsn" => "mysql:dbname=rate_limiter_test;host=localhost",
            "user" => " UNKNOWN NAME ",
            "pass" => "",
        ),

    "otherConf" => array(
            "tableName" => "rate_limiter",
        )
);


//$backyardConf = array();
//
//$backyardConf['logging_level'] = 3;
//$backyardConf['mail_for_admin_enabled'] = 'admin@admin.cz';
//$backyardConf['error_log_message_type'] = 3;
//$backyardConf['logging_file'] = '/var/www/virtual_hosts/logs/error_php.log';
//

