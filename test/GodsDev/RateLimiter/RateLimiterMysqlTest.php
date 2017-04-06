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
    private $limiterWrapper;

    protected function setUp() {
        parent::setUp();

        $connProps = array(
            "dsn" => "mysql:dbname=rate_limiter_test;host=127.0.0.1",
            "user" => "root",
            "pass" => "",
        );
        $this->limiter = new RateLimiterMysql(10, 5, "id1", $connProps);
        $this->limiterWrapper = new LimiterTimeWrapper($this->limiter, true);

        $this->limiterWrapper->reset();
    }



    public function getLimiterTimeWrapper() {
        return $this->limiterWrapper;
    }

//put your code here
}
