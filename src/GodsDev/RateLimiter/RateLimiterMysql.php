<?php

namespace GodsDev\RateLimiter;

/**
 * A RateLimiterInterface implementation using a MySQL db
 *
 *
 * @see RateLimiterInterface
 */
class RateLimiterMysql implements \GodsDev\RateLimiter\RateLimiterInterface {
    private $period;
    private $rate;
    private $userId;
    private $properties;


    public function __construct($rate, $period, $userId, array $properties) {
        $this->rate = $rate;
        $this->period = $period;
        $this->userId = $userId;
        $this->properties = $properties;
    }


    public function getHits() {
        return 0;
    }

    public function getPeriod() {
        return $this->period;
    }

    public function getRate() {
        return $this->rate;
    }

    public function getTimeToWait() {
        return 0;
    }

    public function inc($timestamp = null) {
        return false;
    }

    public function reset($timestamp = null) {
        //TODO implement
    }

}

