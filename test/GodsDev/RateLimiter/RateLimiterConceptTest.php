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
    private $limiter;
    private $limiterWrapper;

    protected function setUp() {
        parent::setUp();

        $this->limiter = new RateLimiterConcept(10, 5);
        $this->limiterWrapper = new LimiterTimeWrapper($this->limiter, true);

        $this->limiterWrapper->reset();
    }


    public function getLimiterTimeWrapper() {
        return $this->limiterWrapper;
    }


    public function testFlow() {
        $this->doTheLimiterFlow();
    }
//put your code here
}
