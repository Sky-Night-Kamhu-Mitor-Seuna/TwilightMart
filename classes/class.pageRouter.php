<?php
/*************************************************************
 * 
 * 頁面選擇器
 * 
*************************************************************/
class pageRouter{
    private $conn;

    private $component_page_table="s_component_page";
    private $pages_table="s_pages";
    private $pageid;
    /************************************************
     * ### pageRouter ###
     * @param obj $db 資料庫
     ************************************************/
    public function __construct($db){
        $this->conn = $db;
    }
    /************************************************
     * ### 設置元件表 ###
     * @param obj $obj 資料表
     ************************************************/
    public function setTables($component_page_table,$pages_table)
    {
        $this->component_page_table = $component_page_table;
        $this->pages_table = $pages_table;
    }
    /************************************************
     * ### 取得頁面資訊 ###
     * @param string $pagename 頁面名稱
     ************************************************/
    public function getPageInfo($pagename) {
        $output=$this->conn->prepare("SELECT `id`,`title`,`description` FROM {$this->pages_table} WHERE link=?;",[$pagename]);
        if(!empty($output)) return $output;
        return null;
    }
    /************************************************
     * ### 取得頁面元件 ###
     * @param string $pagename 頁面名稱
     ************************************************/
    public function getComponentPage($pagename){
        $this->pageid = $this->getPageInfo($pagename)[0]['id'];
        if($this->pageid != null){
            $output=$this->conn->each("SELECT `displayname`, `component_id`, `params` FROM {$this->component_page_table} WHERE `page_id` = $this->pageid ORDER BY position ASC");
            return $output;
        }
        return null;
    }
}
?>