<?php

namespace GodsDev\RateLimiter;

/**
 * A naive, file-based implementation of RateLimiter
 *
 * @author Tomáš Kraus
 */
class RateLimiterFile extends \GodsDev\RateLimiter\AbstractRateLimiter {

    /**
     *
     * @var string
     */
    private $storageFileName;

    /**
     * 
     * @param string $fileName
     * @param int $hits
     * @param int $startTime
     */
    private function openAndWriteData($fileName, $hits, $startTime) {
        $f = fopen($fileName, "w") or die("Unable to open file: [$fileName]");
        fwrite($f, "$hits\n$startTime");
        fclose($f);
    }

    /**
     * 
     * @param string $fileName
     * @return array
     */
    private function openAndReadData($fileName) {
        $f = fopen($fileName, "r") or die("Unable to open file: [$fileName]");
        $resArr = array();
        $resArr["hits"] = intval(fgets($f));
        $resArr["startTime"] = fgets($f);
        fclose($f);
        return $resArr;
    }

    /**
     * 
     * @param int $rate
     * @param int $period
     * @param string $storageFileName each user MUST have its own storageFile
     */
    public function __construct($rate, $period, $storageFileName) {
        parent::__construct($rate, $period);
        $this->storageFileName = $storageFileName;
    }

    /**
     * 
     * @param int $hits
     * @param int $startTime
     */
    protected function readDataImpl(&$hits, &$startTime) {
        if (!file_exists($this->storageFileName)) {
            $this->resetDataImpl($startTime);
        }
        $arr = $this->openAndReadData($this->storageFileName);
        $hits = $arr["hits"];
        $startTime = $arr["startTime"];
    }

    /**
     * 
     * @param int $startTime
     * @return int
     */
    protected function resetDataImpl($startTime) {
        $this->openAndWriteData($this->storageFileName, 0, $startTime);
        return $startTime;
    }

    /**
     * 
     * @param int $lastKnownHitCount
     * @param int $lastKnownStartTime
     * @param int $sanitizedIncrement
     * @return boolean true
     */
    protected function incrementHitImpl($lastKnownHitCount, $lastKnownStartTime, $sanitizedIncrement) {
        $arr = $this->openAndReadData($this->storageFileName);
        $this->openAndWriteData($this->storageFileName, $lastKnownHitCount + $sanitizedIncrement, $arr["startTime"]);
        return true;
    }

}
