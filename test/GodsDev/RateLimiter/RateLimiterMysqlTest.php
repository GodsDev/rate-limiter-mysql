<?php

namespace GodsDev\RateLimiter;

require_once __DIR__ . "/../../../common.php";

use GodsDev\RateLimiter\RateLimiterInterfaceTest;


/**
 * Description of RateLimiterMysqlTest
 *
 * @author Tomáš
 */
class RateLimiterMysqlTest extends RateLimiterInterfaceTest {
    private $limiter;
    private $timeLimiter;

    protected function setUp() {
        parent::setUp();
        $this->limiter = new RateLimiterMysql(10, 5, "id1", array());
        $this->timeLimiter = new LimiterTimeWrapper($this->limiter, true);
    }



    public function getLimiterTimeWrapper() {
        return $this->timeLimiter;
    }

//put your code here
}
