<?php

namespace GodsDev\RateLimiter;

use Assert\Assertion;

/**
 * A RateLimiterInterface implementation using a PDO db layer
 *
 *
 * @see RateLimiterInterface
 */


class RateLimiterMysql extends \GodsDev\RateLimiter\AbstractRateLimiter {
    const DEFAULT_TABLE_NAME = "rate_limiter"; //default rate limiter table name

    private $userId;

    private $otherProps;
    private $tableName;

    private $conn; //PDO connection



    /**
     *
     * creates a PDO db connection object
     *
     *   $connection properties example:
     *    [
     *     'dsn'   => 'mysql:dbname=testdb;host=127.0.0.1',
     *     'user' => 'root',
     *     'pass' => '***',
     *    ]
     *
     * @see http://php.net/manual/en/pdo.construct.php
     */
    public static function createConnectionObj($connectionProperties) {
        Assertion::isArray($connectionProperties);
        try {
            $cp = $connectionProperties;
            $conn = new \PDO($cp["dsn"], $cp["user"], $cp["pass"]
                    , (false) ?
                        array(\PDO::ATTR_PERSISTENT => true)
                        :
                        array()
                );
            $conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $conn;
        } catch (\PDOException $pe) {
            throw new RateLimiterException("createConnectionObj failed", -1, $pe);
        }
    }

    /**
     *
     * @param integer $rate number of requests per period
     * @param integer $period duration of a period
     * @param string $userId unique user identifier
     * @param \PDO $PDOConnection PDO connection to MySQL db. see http://php.net/manual/en/pdo.construct.php
     * @param array $otherProperties
     *
     *   $otherProperties example:
     *    [
     *      'tableName' => 'special_rate_name',
     *    ]
     *
     * @see createConnectionObj
     */
    public function __construct($rate, $period, $userId, \PDO $PDOConnection, array $otherProperties = null) {
        parent::__construct($rate, $period);

        $this->conn = $PDOConnection;

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
        $this->userId = $userId;
    }

    /**
     * @return \PDO a PDO db connection
     */
    private function getConnection() {
        return $this->conn;
    }

    private function releaseConnection() {
        //$this->conn = null;
        //echo "\n  connection released";
    }



    private function release($stmt) {
        self::releaseStatement($stmt);
        $this->releaseConnection();
    }

    private static function releaseStatement($PDOStmt) {
        if ($PDOStmt != null) {
            $PDOStmt->closeCursor();
            $PDOStmt = null;
        }
    }

    protected function incrementHitImpl($lastKnownHitCount, $lastKnownStartTime, $sanitizedIncrement) {
        //TODO: think about dirty read if same id is processed concurrently
        $status = $this->upsertItem($lastKnownHitCount + $sanitizedIncrement, $lastKnownStartTime);
        if ($status) {
            return $sanitizedIncrement;
        } else {
            return 0;
        }
    }


    private function readDataFromExistingRow() {
        $stmt = $this->getConnection()->prepare(
            "SELECT * FROM `{$this->tableName}` WHERE `user_id` = \"{$this->userId}\" LIMIT 1"
        );
        try {
            $stmt->execute();
            if ($stmt->rowCount() < 1) {
                return null;
            } else if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch();
                //var_dump($row);
                $startTime = strtotime($row["start_time"]);
                //var_dump($startTime);
                $hits = $row["hits"];

                return array("startTime" => $startTime, "hits" => $hits);
            } else {
                throw new RateLimiterException("sql in readDataFromExistingRow should return max 1 result");
            }
        } finally {
            $this->release($stmt);
        }
    }


    protected function readDataImpl(&$hits, &$startTime) {
        $data = $this->readDataFromExistingRow();
        if ($data) {
            $hits = $data["hits"];
            $startTime = $data["startTime"];
        } else {
            $this->upsertItem($hits, $startTime);
        }
    }

    protected function resetDataImpl($startTime) {
//        echo "\n--resetDataImpl to " . date("Y-m-d H:i:s", $startTime) . " from "
//                . debug_backtrace()[1]['function']; //get name of a caller function (reset)
        $this->upsertItem(0, $startTime);
        return $startTime;
    }

    private function upsertItem($hits, $startTime) {
        $stmt = $this->getConnection()->prepare(
                    "INSERT INTO `{$this->tableName}` SET `start_time` = FROM_UNIXTIME($startTime), `hits` = $hits, `user_id` = \"{$this->userId}\""
                    . " ON DUPLICATE KEY UPDATE `start_time` = FROM_UNIXTIME($startTime), `hits` = $hits"
                );
        try {
            return $stmt->execute();
        } finally {
            $this->release($stmt);
        }
    }


    public static function deleteItemById($id, $connection, $tableName) {
        $stmt = $connection->prepare(
                    "DELETE FROM `{$tableName}` WHERE `user_id` = \"$id\""
                );
        try {
            return $stmt->execute();
        } finally {
            self::releaseStatement($stmt);
        }
    }
}

