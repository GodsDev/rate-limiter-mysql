<?php

namespace GodsDev\RateLimiter;

use Assert\Assertion;

/**
 * A RateLimiterInterface implementation using a PDO db layer
 *
 *
 * @see RateLimiterInterface
 */
class RateLimiterMysql implements \GodsDev\RateLimiter\RateLimiterInterface {
    const DEFAULT_TABLE_NAME = "rate_limiter"; //default rate limiter table name

    private $period;
    private $rate;
    private $userId;
    private $connProps;
    private $otherProps;
    private $tableName;

    private $conn; //PDO connection

    /**
     *
     * @param integer $rate number of requests per period
     * @param integer $period duration of a period
     * @param string $userId unique user identifier
     * @param array $connectionProperties
     * @param array $otherProperties
     *
     * @throws \PDOException
     *
     * @see http://php.net/manual/en/pdo.construct.php
     *
     *   $connection properties example:
     *    [
     *     'dsn'   => 'mysql:dbname=testdb;host=127.0.0.1',
     *     'user' => 'root',
     *     'pass' => '***',
     *    ]
     *
     *   $otherProperties example:
     *    [
     *      'tableName' => 'alternate_name_instead_of the_default_one',
     *    ]
     *
     */
    public function __construct($rate, $period, $userId, array $connectionProperties, array $otherProperties = null) {

        Assertion::isArray($connectionProperties);

        $default_other_props = array(
                "tableName" => self::DEFAULT_TABLE_NAME,
        );
        if ($otherProperties == null) {
            $this->otherProps = $default_other_props;
        } else {
            Assertion::isArray($otherProperties);
            $this->otherProps = array_merge($default_other_props, $otherProperties);
        }
        $this->tableName = $this->otherProps["tableName"];

        $this->rate = $rate;
        $this->period = $period;
        $this->userId = $userId;
        $this->connProps = $connectionProperties;

        $cp = $this->connProps;
        $this->conn = new \PDO($cp["dsn"], $cp["user"], $cp["pass"]);
        $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }


    protected function getTimestamp($timestamp) {
        if ($timestamp != null) {
            $val = $timestamp;
        } else {
            $val = time();
        }
        return date("Y-m-d H:i:s", $val);
    }


    protected function updateStatus($timestamp = null) {
        $tooOldTime = $this->getTimestamp($timestamp) - $this->period;
        $this->conn->exec(
            "UPDATE `{$this->tableName}` SET `hits` = 0 WHERE `id` = {$this->userId} AND `timestamp` < {$tooOldTime}");
    }


    public function getHits($timestamp = null) {
        $res = $this->conn->query("SELECT * FROM `{$this->tableName}` WHERE `user_id` = \"{$this->userId}\""
            //, \PDO::FETCH_ASSOC
                );
        //var_dump($res);
        $hits = intval($res->fetchColumn(3));
        //var_dump($hits);
        return $hits;
    }

    public function getPeriod() {
        return $this->period;
    }

    public function getRate() {
        return $this->rate;
    }

    public function getTimeToWait($timestamp = null) {
        return 0;
    }

    public function inc($timestamp = null) {
        //$this->conn->query("INSERT INTO `{$this->tableName}` (hits, timestamp) VALUES((`hits` + 1), {$this->getTimestamp($timestamp)}) WHERE `user_id` = \"{$this->userId}\" ON DUPLICATE KEY UPDATE timestamp = VALUES(timestamp)");

        $numAffected = $this->conn->exec("INSERT INTO `rate_limiter` SET `timestamp` = \"{$this->getTimestamp($timestamp)}\", `user_id` = \"{$this->userId}\""
            . ", `hits` = (hits + 1) ON DUPLICATE KEY UPDATE user_id = \"id1\", hits = (hits + 1)");

        return ($numAffected > 0);
    }

    public function reset($timestamp = null) {
        $numAffected = $this->conn->exec("DELETE FROM `rate_limiter` WHERE `user_id` = \"{$this->userId}\"");
    }

}

