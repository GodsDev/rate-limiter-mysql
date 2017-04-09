<?php

namespace GodsDev\RateLimiter;

/*
 *
 */

class RateLimiterConcept extends \GodsDev\RateLimiter\AbstractRateLimiter {

    private $cHits;
    private $cStartTime;

    private $dataCreated;

    public function __construct($rate, $period) {
        parent::__construct($rate, $period);
        $this->dataCreated = false;
    }

    protected function readDataImpl(&$hits, &$startTime) {
        if (!$this->dataCreated) {
            return false;
        } else {
            $hits = $this->cHits;
            $startTime = $this->cStartTime;
            return true;
        }
    }

    protected function resetDataImpl($startTime) {
        $this->cStartTime = $startTime;
        $this->cHits = 0;
    }

    protected function incrementHitImpl() {
        $this->cHits++;
    }

    protected function createDataImpl($startTime) {
        $this->cHits = 0;
        $this->cStartTime = $startTime;
        $this->dataCreated = true;
    }

}
