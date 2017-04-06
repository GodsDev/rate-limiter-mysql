<?php

namespace GodsDev\RateLimiter;

/*
 *
 */

class RateLimiterConcept implements \GodsDev\RateLimiter\RateLimiterInterface {
    private $rate;
    private $period;

    private $hits;
    private $startTime;
    private $timeToWait;

    public function __construct($rate, $period) {
        $this->rate = $rate;
        $this->period = $period;
    }

    public function getHits($timestamp = null) {
        return $this->hits;
    }

    public function getPeriod() {
        return $this->period;
    }

    public function getRate() {
        return $this->rate;
    }

    private function refreshState($timestamp) {
        if ($timestamp == null) {
            $timestamp = time();
        }
        $this->timeToWait = $this->period - ($timestamp - $this->startTime);
        if ($this->timeToWait <= 0) {
            $this->timeToWait = 0;
            $this->hits = 0;
            $this->startTime = $timestamp;
        } else if ($this->hits < $this->rate) {
            $this->timeToWait = 0;
        }
    }

    public function getTimeToWait($timestamp = null) {
        $this->refreshState($timestamp);
        return $this->timeToWait;
    }

    public function inc($timestamp = null) {
        $this->refreshState($timestamp);
        if ($this->timeToWait == 0 && $this->hits < $this->rate) {
            $this->hits++;
            return true;
        } else {
            return false;
        }
    }

    public function reset($timestamp = null) {
        $this->startTime = $timestamp;
        $this->hits = 0;
    }

}