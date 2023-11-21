<?php
//PHP_INT_SIZE: 8
/*************************************************************
 * 
 * ### 雪花算法產生器 ###
 * @param int $dataCenterId 數據中心ID (默認0)
 * @param int $workerId 工作站ID (默認0)
*************************************************************/
class snowflake {
    private static $startTimeStamp = 1526054400000;     // 起始時間戳，這邊是2018年5月12日 00:00:00.0
    private static $dataCenterIdBits=5;                 // 數據中心ID位數(Bit)
    private static $workerIdBits=5;                     // 工作機ID位數(Bit)
    private static $sequenceBits=12;                    // 限制同一毫秒下最高可展生之序列號數量之位元
    private static $sequenceMask;                       // 序列掩碼
    private static $maxDataCenterId;                    // 最大數據中心ID
    private static $maxWorkerId;                        // 最大工作機ID 
    private $dataCenterId;                              // 數據中心ID
    private $workerId;                                  // 工作機ID
    private $lastTimestamp = -1;                        // 上一個刷出來的時間戳
    private $sequence = 0;                              // 序列號
    /************************************************
     * ### 初始化 ###
    ************************************************/
    public function __construct($dataCenterId=31, $workerId=31) {
        //self::$sequenceBits = $sequenceBits;
        self::$maxDataCenterId = -1 ^ (-1 << self::$dataCenterIdBits);
        self::$maxWorkerId = -1 ^ (-1 << self::$workerIdBits);
        self::$sequenceMask = -1 ^ (-1 << self::$sequenceBits);

        // 值域必須在範圍餒 由 $workerIdBits 及 $maxDataCenterId 決定
        if ($dataCenterId > self::$maxDataCenterId || $dataCenterId < 0) throw new Exception("[WANG] The DataCenter ID must be within the range of 0 and ".self::$maxDataCenterId);
        if ($workerId > self::$maxWorkerId || $workerId < 0) throw new Exception("[WANG] The Woker ID must be within the range of 0 and ".self::$maxDataCenterId);
        $this->dataCenterId = $dataCenterId;
        $this->workerId = $workerId;
    }

    /************************************************
     * ### 取得時間戳記根 ###
     * 產生Timestamp(41位元)
    ************************************************/
    private function timeGen() : int {
        return (int)(microtime(true) * 1000);
    }

    /************************************************
     * ### 取得下一個時間戳 ###
    ************************************************/
    private function tilNextMillis($lastTimestamp) : int {
        $timestamp = $this->timeGen();
        while ($timestamp <= $lastTimestamp) $timestamp = $this->timeGen();
        return $timestamp;
    }
    
    /************************************************
     * ### 取得一個雪花算法ID ###  
     *  1 - 38 Bits $timestamp - 2018年5月12日00:00:00.0   
     * 39 - 43 Bits $dataCenterId之保留位元  
     * 44 - 48 Bits $workerId之保留位元  
     * 49 - 60 Bits $sequenceBits之保留位元  
    ************************************************/
    public function getId() : int {
        $timestamp = $this->timeGen();

        // 系統鐘時間產生錯誤
        if ($timestamp < $this->lastTimestamp) throw new Exception("[WANG] Clock moved backwards.");

        // 確認時間戳是否重複
        if ($this->lastTimestamp == $timestamp) {
            $this->sequence = ($this->sequence + 1) & self::$sequenceMask;
            if ($this->sequence == 0) $timestamp = $this->tilNextMillis($this->lastTimestamp);
        } else $this->sequence = 0;

        $this->lastTimestamp = $timestamp;
        
        // 回傳snowflakeID
        return (($timestamp - self::$startTimeStamp) << self::$sequenceBits + self::$workerIdBits + self::$dataCenterIdBits) |
               ($this->dataCenterId << self::$sequenceBits + self::$workerIdBits) |
               ($this->workerId << self::$sequenceBits) |
               $this->sequence;
    }
}

?>