<?php
// include "./class.connectDatabase.php";
/*************************************************************
 * 
 * 頁面選擇器 : 用來確認頁面有哪些元件
 * @param obj $db 資料庫
 * 
*************************************************************/
class pageRouter{
    private $conn;

    private $pageComponent="w_page_component";
    private $pages="w_pages";
    private $components="s_components";
    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 取得頁面資訊 ###
     * @param string $pagename 頁面名稱
     * @param string $wid 網站編號  
     * 這個func非常重要，他也是輸出網站pid的來源
     ************************************************/
    public function getPageInfomation($pagename, $wid) : array
    {
        $sql = "SELECT `id`, `displayname`, `description`, `icon` FROM {$this->pages} WHERE `name` = ? AND `wid` = ? AND `status` <> 0;";
        $row = $this->conn->prepare($sql,[$pagename, $wid]);
        return $row;
    }
    /************************************************
     * ### 取得頁面元件 ###
     * @param string $pid 頁面編號
     ************************************************/
    public function getPageComponent($pid) : array
    {
        $sql = "SELECT `page_component`.`id`, `page_component`.`displayname`, `components`.`name`, `components`.`template`, `page_component`.`params`, `page_component`.`permissions` 
        FROM {$this->pageComponent} AS `page_component`
        JOIN {$this->components} AS `components` ON `components`.`id` = `page_component`.`cid`
        WHERE `pid` = ? AND `status` <> 0 ORDER BY `position` ASC;";
        $row = $this->conn->prepare($sql, [$pid]);
        return $row;
    }
}
?>
