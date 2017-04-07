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
        $this->reset();
    }

    public function getHits($timestamp = null) {
        $this->refreshState($timestamp);
        return $this->hits;
    }

    public function getPeriod() {
        return $this->period;
    }

    public function getRate() {
        return $this->rate;
    }

    private function refreshState($timestamp) {
        if (is_null($timestamp)) {
            $timestamp = time();
        }
        if ($timestamp - $this->startTime >= $this->period) {
            //a new, clean period
            $this->reset($timestamp);
            //echo("**clean period  [(start=$this->startTime)]**");
        } else if ($this->hits < $this->rate) {
            $this->timeToWait = 0;
            //echo("**hits<rate  [(start=$this->startTime) $this->hits<$this->rate]**");
        } else {
            $this->timeToWait = intval( ceil($this->period - ($timestamp - $this->startTime)) );
            //echo("**time to wait [(per=$this->period, ts=$timestamp, start=$this->startTime) $this->timeToWait]**");
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
        if (is_null($timestamp)) {
            $this->startTime = time();
            //echo("^^C startTime reset to [$this->startTime] ^^");
        } else {
            $this->startTime = $timestamp;
            //echo("^^C startTime reset to preset [$this->startTime] ^^");
        }

        $this->hits = 0;
        $this->timeToWait = 0;
    }

}