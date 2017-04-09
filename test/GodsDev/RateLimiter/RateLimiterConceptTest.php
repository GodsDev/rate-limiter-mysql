<?php

namespace GodsDev\RateLimiter;

require_once __DIR__ . "/../../../common.php";

use GodsDev\RateLimiter\RateLimiterInterfaceTest;


/**
 * Description of RateLimiterMysqlTest
 *
 * @author Tomáš
 */
class RateLimiterConceptTest extends AbstractRateLimiterInterfaceTest {

    public function createRateLimiter($rate, $period) {
        return new \GodsDev\RateLimiter\RateLimiterConcept($rate, $period);
    }

    protected function setUp() {
        parent::setUp();
    }

}
