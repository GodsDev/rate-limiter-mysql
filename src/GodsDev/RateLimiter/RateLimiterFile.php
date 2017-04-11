<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace GodsDev\RateLimiter;

/**
 * A naïve, file-based implementation of RateLimiter
 *
 * @author Tomáš
 */
class RateLimiterFile extends \GodsDev\RateLimiter\AbstractRateLimiter {
    private $storageFileName;

    private function openAndWriteData($fileName, $hits, $startTime) {
        $f = fopen($fileName, "w") or die("Unable to open file: [$fileName]");
        fwrite($f, "$hits\n$startTime" );
        fclose($f);
    }

    private function openAndReadData($fileName) {
        $f = fopen($fileName, "r") or die("Unable to open file: [$fileName]");
        $resArr = array();
        $resArr["hits"] = intval( fgets($f) );
        $resArr["startTime"] = fgets($f);
        fclose($f);
        return $resArr;
    }


    public function __construct($rate, $period, $storageFileName) {
        parent::__construct($rate, $period);
        $this->storageFileName = $storageFileName;
    }


    protected function readDataImpl(&$hits, &$startTime) {
        if (!file_exists($this->storageFileName)) {
            $this->resetDataImpl($startTime);
        }
        $arr = $this->openAndReadData($this->storageFileName);
        $hits = $arr["hits"];
        $startTime = $arr["startTime"];

    }

    protected function resetDataImpl($startTime) {
        $this->openAndWriteData($this->storageFileName, 0, $startTime);
        return $startTime;
    }

    protected function incrementHitImpl() {
        $arr = $this->openAndReadData($this->storageFileName);
        $this->openAndWriteData($this->storageFileName, ++$arr["hits"], $arr["startTime"]);
        return true;
    }

}
