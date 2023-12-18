<?php
/*************************************************************
 * 
 * 頁面管理 : 增刪頁面
 * @param obj $db 資料庫
 * 
*************************************************************/
class pageManage{
    private $conn;
    // private $pageComponent="w_page_component";
    private $pages="w_pages";
    
    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 取得頁面資訊 ###
     * @param string $wid 網站編號
     * @param string $pagename 頁面名稱
     ************************************************/
    private function checkPage($pid) : int 
    {
        $sql = "SELECT `id` FROM {$this->pages} WHERE `id` = ? AND `status` <> 0;";
        $row = $this->conn->prepare($sql,[$pid]);
        $result = empty($row) ? false : $row[0]['id'];
        return $result;
    }
    /************************************************
     * ### 新增頁面 ###
     * @param obj $db 資料庫
     ************************************************/
    public function findPageInformation($wid) : array
    {   
        $sql = "SELECT * FROM {$this->pages} WHERE `wid` = ? AND `status` <> 0;";
        $row = $this->conn->prepare($sql, [$wid]);
        return $row;
    }
    /************************************************
     * ### 新增頁面 ###
     * @param obj $db 資料庫
     ************************************************/
    public function addPage($pid, $wid, $pagename, $displayname) : bool
    {
        $check = $this->checkPage($pid);
        if( !empty( $check ) ) return false;
        
        $sql = "INSERT INTO {$this->pages} (`id`, `wid`, `name`, `displayname`) VALUES (?, ?, ?, ?)";
        $row = $this->conn->prepare($sql, [$pid, $wid, $pagename, $displayname]);
        return empty($row);
    }
    /************************************************
     * ### 刪除頁面 ###
     * @param obj $db 資料庫
     ************************************************/
    public function changePageStatus($pid,$action = "DISABLE") : bool
    {
        // 2為系統頁面，不能刪除
        $check = $this->checkPage($pid);
        if( empty($check) || $check == 2 ) return false;
        $status=0;
        
        $sql = "UPDATE {$this->pages} SET `status` = ? WHERE `id` = ?";
        $row = $this->conn->prepare($sql, [$status, $pid]);
        return empty($row);
    }
    /************************************************
     * ### 編輯頁面 ###
     * @param obj $db 資料庫
     ************************************************/
    public function editPage($pid, $name, $displayname, $description) : bool
    {
        $check = $this->checkPage($pid);
        if( empty( $check ) ) return false;
        
        $sql = "UPDATE {$this->pages} SET `name` = ?, `displayname` = ?, `description` = ? WHERE `id` = ?";
        $row = $this->conn->prepare($sql, [$name, $displayname, $description, $pid]);
        return empty($row);
    }
}