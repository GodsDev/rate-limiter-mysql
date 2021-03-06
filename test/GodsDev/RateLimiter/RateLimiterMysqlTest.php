<?php

namespace GodsDev\RateLimiter;

require_once __DIR__ . "/../../../common.php";


/**
 * Description of RateLimiterMysqlTest
 *
 * @author Tomáš
 */
class RateLimiterMysqlTest extends AbstractRateLimiterInterfaceTest {

    private $connection;
    private $otherProps;

    const ID_1 = "id_1";
    const ID_A = "id_A";
    const ID_B = "id_B";


    protected function setUp() {
        global $config;

        $this->otherProps = $config["otherConf"];
        $this->connection = RateLimiterMysql::createConnectionObj($config["dbConnection"]);
        $this->deleteIds($this->connection);

        parent::setUp();
    }

    protected function tearDown() {
        //close the connection by destroying the object (see php PDO)
        $this->connection = null;

        parent::tearDown();
    }


    private function deleteIds($connection) {
        foreach ([self::ID_1, self::ID_A, self::ID_B] as $id) {
            RateLimiterMysql::deleteItemById($id, $connection, $this->otherProps["tableName"]);
        }
    }

    public function createRateLimiter($rate, $period) {
        return new \GodsDev\RateLimiter\RateLimiterMysql($rate, $period, self::ID_1, $this->connection, $this->otherProps);
    }


    public function test_CreateConnectionObj_From_Bad_Config_Throws_RateLimiterException() {
        global $config;

        $this->setExpectedException(RateLimiterException::class);
        $conn = RateLimiterMysql::createConnectionObj($config["dbConnectionMisconfiguration"]);
    }


    public function test_Two_Limiters_With_Different_ID_Should_Behave_Independently() {

        $limA = new RateLimiterMysql(3, 20, self::ID_A, $this->connection, null);
        $limB = new RateLimiterMysql(3, 20, self::ID_B, $this->connection, null);
        $startTime = time();
        //echo "--------start=" . date("Y-m-d H:i:s", $startTime);

        $t = $startTime;

        $st_A = $limA->getStartTime($t);
        //$this->assertLessThanOrEqual(1, 2);  //fail

        $this->assertLessThanOrEqual($t, $st_A);
        $this->assertEquals(1, $limA->inc($t), "limA inc(t)");
        $this->assertEquals(1, $limA->getHits($t), "limA getHits(t), is timezone set properly?");

        $t += 10;

        $st_B = $limB->getStartTime($t);
        $this->assertLessThanOrEqual($t, $st_B);

        $this->assertLessThanOrEqual($st_B, $st_A);

        $this->assertEquals(1, $limA->inc($t));
        $this->assertEquals(1, $limA->inc($t));
        $this->assertEquals(0, $limA->inc($t));

        $this->assertEquals(1, $limB->inc($t));

        $this->assertEquals(3, $limA->getHits($t));
        $this->assertEquals(1, $limB->getHits($t));

        $this->assertEquals(10, $limA->getTimeToWait($t));

        $t += 10;

        $this->assertEquals(0, $limA->getHits($t));
        $this->assertEquals(0, $limA->getTimeToWait($t));

        $this->assertEquals(1, $limB->getHits($t));
        $this->assertEquals(1, $limB->inc($t));
        $this->assertEquals(2, $limB->getHits($t));

        $t += 6;

        $this->assertEquals(1, $limB->inc($t));
        $this->assertEquals(3, $limB->getHits($t));
        $this->assertEquals(4, $limB->getTimeToWait($t));

        $this->assertEquals(1, $limA->inc($t));
        $this->assertEquals(1, $limA->getHits($t));
        $this->assertEquals(0, $limA->getTimeToWait($t));

        $t += 4;

        $this->assertEquals(0, $limB->getHits($t));
        $this->assertEquals(0, $limB->getTimeToWait($t));

        $this->assertEquals(1, $limA->inc($t));
        $this->assertEquals(1, $limA->inc($t));
        $this->assertEquals(3, $limA->getHits($t));
        $this->assertEquals(10, $limA->getTimeToWait($t));
    }
}
