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
class RateLimiterFileTest extends AbstractRateLimiterInterfaceTest {

    private $storageFilename = __DIR__ . "/../../../temp/storage-test.txt";

    public function createRateLimiter($rate, $period) {
        return new \GodsDev\RateLimiter\RateLimiterFile($rate, $period, $this->storageFilename);
    }

    public function setUp() {
        if (file_exists($this->storageFilename)) {
            unlink($this->storageFilename);
            sleep(1);   //need some time before the system deletes the file
        }
        parent::setUp();
    }
}
