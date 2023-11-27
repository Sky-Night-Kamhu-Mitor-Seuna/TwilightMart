<?php
// include "./class.connectDatabase.php";
/****************************************************************************************
 * 
 * 頁面元件
 * @param obj $db 資料庫
 * 
 ****************************************************************************************/
class componentPage
{
    private $conn;
    private $pageComponent = 'w_page_component';
    private $pages = 'w_pages';
    private $components = 's_components';

    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 確認元件預設參數及權限 ###
     * @param int $cid 元件編號
     ************************************************/
    private function getComponentParams($cid) : array
    {
        $sql = "SELECT `params`,`permissions` FROM `{$this->components}` WHERE `id` = ? ;";
        $row = $this->conn->prepare($sql,[$cid]);
        return $row[0];
    }
    /************************************************
     * ### 取得新的元件位址 ###
     * @param int $pid 頁面編號
     ************************************************/
    private function getPageComponentPosition($pid) : int
    {
        $sql = "SELECT `position` FROM `{$this->pageComponent}` WHERE `pid`= ? AND `status` <> 0 ORDER BY `position` DESC LIMIT 1";
        $row = $this->conn->prepare($sql, [$pid]);
        $result = empty($row) ? 0 : $row[0]['position'];
        return $result;
    }
    /************************************************
     * ### 取得特定位置元件之ID ###
     * @param int $id 產生的頁面元件編號
     * @param int $position 元件位置
     ************************************************/
    // private function getPageComponentId($pid, $position) : int
    // {
    //     $sql = "SELECT `id` FROM `{$this->pageComponent}` WHERE `pid` = ? AND `position` = ? AND `status` <> 0;";
    //     $row = $this->conn->prepare($sql,[$pid, $position]);
    //     $result = empty($row) ? 0 : $row[0][`id`] ;
    //     return $result;
    // }
    /************************************************
     * ### 取得頁面參數位址等資訊 ###
     * @param int $id 產生的頁面元件編號
     ************************************************/
    private function getPageComponentsInfo($id) : array
    {
        $sql = "SELECT `displayname`, `params` FROM `{$this->pageComponent}` WHERE `id` = ? AND `status` <> 0;";
        $row = $this->conn->prepare($sql,[$id]);
        $result = empty($row) ? array() : $row[0] ;
        return $result;
    }
    /************************************************
     * ### 新增元件與頁面的關聯 ###
     * @param int $id 產生的頁面元件編號
     * @param int $cid 元件編號
     * @param int $pid 頁面編號
     * @param string $displayname 元件命名
     * @param json $params 參數
     ************************************************/
    public function addPageComponents($id, $pid, $cid, $displayname="New Component") : bool
    {
        $componentDefaultParam = $this->getComponentParams($cid);
        // 不存在之元件
        if(empty($componentDefaultParam)) return false;
        $newPosition = $this->getPageComponentPosition($pid) + 1;
        
        $sql = "INSERT INTO `{$this->pageComponent}` (`id`, `pid`, `cid`, `displayname`, `position`, `params`, `permissions`) 
        VALUES (?, ?, ?, ?, ?, ?, ?)";
        $row = $this->conn->prepare($sql, [$id, $pid, $cid, $displayname, $newPosition, $componentDefaultParam['params'], $componentDefaultParam['permissions']]);
        return empty($row);
    }
    /************************************************
     * ### 更新頁面參數名稱等資訊 ###
     * @param int $id 產生的頁面元件編號
     * @param string $displayname 元件命名
     * @param json $params 參數
     ************************************************/
    public function editPageComponents($id, $params=null, $displayname=null)
    {
        $info = $this->getPageComponentsInfo($id);
        if(empty($info)) return false;

        $displayname = is_null($displayname) ? $info['displayname'] : $displayname;
        $params = is_null($params) ? $info['params'] : $params;
        
        $sql = "UPDATE `{$this->pageComponent}` SET `displayname` = ?, `params` = ? WHERE `id` = ?";
        $row = $this->conn->prepare($sql,[$displayname, $params, $id]);
        return empty($row);
    }
    /************************************************
     * ### 刪除元件與頁面的關聯 ###
     * @param int $id 產生的頁面元件編號
     ************************************************/
    public function deletePageComponents($id)
    {
        if(empty($this->getPageComponentsInfo($id))) return false;
        $sql = "UPDATE `{$this->pageComponent}` SET `status` = 0 WHERE `id` = ?";
        $row = $this->conn->prepare($sql,[$id]);
        return empty($row);
    }
    /************************************************
     ************************************************/
    // public function swapPageComponentsPosition($id, $params=null, $displayname=null, $position=null){

    // }
    
}
