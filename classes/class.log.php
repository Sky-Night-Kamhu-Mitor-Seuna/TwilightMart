<?php
/*************************************************************
 * 
 * 系統操作紀錄表
 * @param obj $db 資料庫
 * 
*************************************************************/
class syslog{
    private $conn;
    private $vigilante = true;        // 用於監視使用者行為並且記錄
    private $systemLog = "log_system";
    private $viewLog = "log_page_views";
    private $productVewsLog = "log_product_page_views";
    private $searchLog = "log_search";
    private $timeRegex = '/^\d{4}-\d{2}-\d{2}$/';
    private $dataLimit = 1000;
    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 取得log ###
     * @param string $action S: 系統紀錄 V: 檢視紀錄 P: 商品紀錄
     * @param string $id 網站編號/頁面編號/商品編號
     * @param int $page 分頁檔(用於迴圈讀出大數據)
     * @param string $startTime 開始時間
     * @param string $endTime 結束時間
     ************************************************/
    private function getLog($action="SYSTEM", $id, $page=1, $startTime="0000-01-01", $endTime="9999-12-31") : array
    {
        if (!preg_match($this->timeRegex, $startTime) || !preg_match($this->timeRegex, $endTime)) return [];
        $action = strtoupper($action);
        $logTable = $this->systemLog;
        $field = 'wid';
        switch($action)
        {
            case "V":
            case "VIEW":
                $logTable = $this->viewLog;
                $field = 'pid';
                break;
            case "P":
            case "PRODUCT":
                $logTable = $this->productVewsLog;
                $field = 'product_id';
                break;
        }
        $sql =  "SELECT COUNT(*) FROM `{$logTable}` WHERE `{$field}` = ? AND `created_at` BETWEEN ? AND ?;";
        $row = $this->conn->prepare($sql, [$id, $startTime, $endTime]);
        if( empty($row) ) return [];
        // $page = 1;
        $totalPages = ceil($row[0]['COUNT(*)'] / $this->dataLimit); // 總頁數
    
        if ($page < 1 || $page > $totalPages) return [];
        $offset = ($page - 1) * $this->dataLimit;
        $sql =  "SELECT * FROM `{$this->systemLog}` WHERE `{$field}` = ? AND `created_at` BETWEEN ? AND ? LIMIT ?, ?;";
        $result = $this->conn->prepare($sql, [$id, $startTime, $endTime, $offset, $this->dataLimit]);
        
        return $result;
    }
    /************************************************
     * ### 取得系統記錄檔 ###
     * @param string $wid 網站編號
     * @param int $page 分頁檔(用於迴圈讀出大數據)
     * @param string $startTime 開始時間
     * @param string $endTime 結束時間
     ************************************************/
    public function getSystemLog($wid, $page=1, $startTime="0000-01-01", $endTime="9999-12-31") : array
    {
        return $this->getLog("SYSTEM", $wid, $page, $startTime, $endTime);
    }
    /************************************************
     * ### 取得商品記錄檔 ###
     * @param string $product_id 商品編號
     * @param int $page 分頁檔(用於迴圈讀出大數據)
     * @param string $startTime 開始時間
     * @param string $endTime 結束時間
     ************************************************/
    public function getProductLog($product_id, $page=1, $startTime="0000-01-01", $endTime="9999-12-31") : array
    {
        return $this->getLog("PRODUCT", $product_id, $page, $startTime, $endTime);
    }
    /************************************************
     * ### 取得頁面記錄檔 ###
     * @param string $pid 頁面編號
     * @param int $page 分頁檔(用於迴圈讀出大數據)
     * @param string $startTime 開始時間
     * @param string $endTime 結束時間
     ************************************************/
    public function getViewLog($pid, $page=1, $startTime="0000-01-01", $endTime="9999-12-31") : array
    {
        return $this->getLog("VIEW", $pid, $page, $startTime, $endTime);
    }
    /************************************************
     * ### 設置系統調用單次上限 ###
     * @param int $max 上限值
     ************************************************/
    public function setLimit($max) : bool
    {
        if( !(is_int($max)) || ($max < 1) ) return false;
        $this->dataLimit = $max;
        return true;
    }
    /************************************************
     * ### 寫入系統log ###
     * @param string $id 記錄檔編號
     * @param string $wid 網站編號
     * @param string $operator 操作者
     * @param string $ip_address 網際網路位址
     * @param string $action 操作
     * @param string $status 操作是否成功 0:無效操作 1:存取成功 2:存取被拒
     ************************************************/
    public function addSystemLog($id, $wid, $operator, $ipAddress, $action, $status=1 ) : bool
    {
        // if (empty($action) || !(is_int($status))) return false;
        $hash = hash("sha256", $id);
        $sql = "INSERT INTO `{$this->systemLog}` (`id`, `wid`, `operator`, `ip_address`, `status`, `action`, `hash`) VALUES (?, ?, ?, ?, ?, ?, ?);";
        $result = $this->conn->prepare($sql, [$id, $wid, $operator, $ipAddress, $status, $action, $hash]);
        
        return empty($result);
    }
    /************************************************
     * ### 寫入頁面觀看log ###
     * @param string $id 記錄檔編號
     * @param string $operator 操作者
     * @param string $pid 頁面編號
     * @param string $ip_address 網際網路位址
     * @param string $member_agent 使用者裝置
     * @param string $referrerUrl 來自哪個網站
     * @param string $duration 觀看秒數
     ************************************************/
    public function addViewLog($id, $operator, $pid, $ipAddress, $member_agent, $duration=0, $referrerUrl=null) : bool
    {
        // if (empty($action) || !(is_int($status))) return false;
        $sql = "INSERT INTO `{$this->viewLog}` (`id`, `operator`, `pid`, `ip_address`, `member_agent`, `referrer_url`, `duration`) VALUES (?, ?, ?, ?, ?, ?, ?);";
        $result = $this->conn->prepare($sql, [$id, $operator, $pid, $ipAddress, $member_agent, $referrerUrl, $duration]);
        
        return empty($result);
    }
    /************************************************
     * ### 寫入頁面觀看log ###
     * @param string $id 記錄檔編號
     ************************************************/
    public function addSearchLog($id, $operator, $pid, $ipAddress, $member_agent, $duration=0, $referrerUrl=null) : bool
    {
        // if (empty($action) || !(is_int($status))) return false;
        $sql = "INSERT INTO `{$this->viewLog}` (`id`, `operator`, `pid`, `ip_address`, `member_agent`, `referrer_url`, `duration`) VALUES (?, ?, ?, ?, ?, ?, ?);";
        $result = $this->conn->prepare($sql, [$id, $operator, $pid, $ipAddress, $member_agent, $referrerUrl, $duration]);
        
        return empty($result);
    }
}