<?php

namespace GodsDev\RateLimiter;

/*
 *
 */

class RateLimiterConcept extends \GodsDev\RateLimiter\RateLimiterAdapter {

    private $dataCreated;

    public function __construct($rate, $period) {
        parent::__construct($rate, $period);
        $this->dataCreated = false;
    }

    protected function fetchDataImpl(&$hits, &$startTime) {
        if (!$this->dataCreated) {
            return false;
        } else {
            $hits = $this->hits;
            $startTime = $this->startTime;
            return true;
        }
    }

    protected function resetDataImpl($hits, $startTime) {
        $this->hits = $hits;
        $this->startTime = $startTime;
    }

    protected function storeHitsImpl($hits) {
        $this->hits = $hits;
    }

    protected function createDataImpl($hits, $startTime) {
        //echo(" s:$startTime");
        //$this->reset($startTime);
        $this->dataCreated = true;
    }

}