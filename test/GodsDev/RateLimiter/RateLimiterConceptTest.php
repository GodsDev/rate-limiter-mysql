<?php

namespace GodsDev\RateLimiter;

require_once __DIR__ . "/../../../common.php";

use GodsDev\RateLimiter\RateLimiterInterfaceTest;


/**
 * Description of RateLimiterMysqlTest
 *
 * @author Tomáš
 */
class RateLimiterConceptTest extends RateLimiterInterfaceTest {


    public function createRateLimiterTimeWrapper(\GodsDev\RateLimiter\RateLimiterInterface $rateLimiter) {
        //return new LimiterTimeWrapper($rateLimiter, true); //real time wait
        return new RateLimiterTimeWrapper($rateLimiter, false);
    }

    public function createRateLimiter() {
        //return new \GodsDev\RateLimiter\RateLimiterConcept(10000, 3600*24); //10k/day
        return new \GodsDev\RateLimiter\RateLimiterConcept(10, 5);
    }

    protected function setUp() {
        parent::setUp();
    }

    public function getLimiterTimeWrapper() {
        return $this->limiterWrapper;
    }

}
