<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace GodsDev\RateLimiter;

/**
 * Description of RateLimiterFileTest
 *
 * @author Tomáš
 */
class RateLimiterFileTest  extends RateLimiterInterfaceTest {
    public function createRateLimiterTimeWrapper(RateLimiterInterface $rateLimiter) {
        return new RateLimiterTimeWrapper($rateLimiter, false);
    }

    public function createRateLimiter() {
        $filename = __DIR__ . "/../../../temp/storage-test.txt";
        //echo("\nstorage file: [$filename]\n");
        return new \GodsDev\RateLimiter\RateLimiterFile(10, 5, $filename);
    }

}
