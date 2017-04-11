<?php

namespace GodsDev\RateLimiter;

/*
 *
 */

class RateLimiterConcept extends \GodsDev\RateLimiter\AbstractRateLimiter {

    private $cHits;
    private $cStartTime;


    public function __construct($rate, $period) {
        parent::__construct($rate, $period);
    }

    protected function readDataImpl(&$hits, &$startTime) {
            $hits = $this->cHits;
            $startTime = $this->cStartTime;
    }

    protected function resetDataImpl($startTime) {
        $this->cStartTime = $startTime;
        $this->cHits = 0;
        
        return $this->cStartTime;
    }

    protected function incrementHitImpl() {
        $this->cHits++;
        return true;
    }


}
