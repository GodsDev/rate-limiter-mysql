<?php

namespace GodsDev\RateLimiter;

/**
 * RateLimiterFileTest uses tests from AbstractRateLimiterInterfaceTest
 * Only RateLimiter using file is created to be tested
 *
 * @author Tomáš Kraus
 */
class RateLimiterFileTest extends AbstractRateLimiterInterfaceTest {

    private $storageFilename; //version1 fails with some tests = __DIR__ . "/../../../temp/storage-test.txt";

    public function createRateLimiter($rate, $period) {
        //version2 and 3 (temp folder needs to be purged from time to time)
        $this->storageFilename = __DIR__ . "/../../../temp/storage-test-" . uniqid(rand(), true) . ".txt";
        return new \GodsDev\RateLimiter\RateLimiterFile($rate, $period, $this->storageFilename);
    }

    public function setUp() {
//version1 fails with tests: GodsDev\RateLimiter\RateLimiterFileTest::test_Ready_In_The_Next_Period and GodsDev\RateLimiter\RateLimiterFileTest::test_StartTime_Is_Within_TimeWindow_Active_State
//        if (file_exists($this->storageFilename)) {
//            unlink($this->storageFilename);
//            sleep(1);   //need some time before the system deletes the file
//        }
        parent::setUp();
    }

    public function tearDown() {
        //version2 fails with GodsDev\RateLimiter\RateLimiterFileTest::test_At_Least_1_Hit_Per_1_TimeUnit and GodsDev\RateLimiter\RateLimiterFileTest::test_Implements_RateLimiterInterface
//        unlink($this->storageFilename);
        
        parent::tearDown();
    }

}
