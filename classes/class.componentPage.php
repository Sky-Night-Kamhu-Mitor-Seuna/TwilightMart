<?php
/*************************************************************
 * 
 * 新增一個頁面元件
 * 
*************************************************************/
class componentPage{
  private $conn;
  private $component_page_table='s_component_page';
  //private $components_table='s_components';
  //private $pages_table='s_pages';
  public $uuid;
  public $component_id;
  public $page_id;
  public $position;
  public $params;

  public function __construct($db) {
    try{ $this->conn = $db; }
    catch(Exception $e){echo $e;}
  }
  /************************************************
   * ### 新增元件與頁面的關聯 ###
   * @param int $component_id 元件編號
   * @param int $page_id 頁面編號
   * @param json $params 參數
   ************************************************/
  function create($component_id,$page_id,$params) {
    $this->uuid = $this->createUUID();
    $this->component_id = $component_id;
    $this->page_id = $page_id;
    $this->position = 0;
    $this->params = $params;
    try{
      $query =  "SELECT position {$this->component_page_table} WHERE page_id = ? ORDER BY position DESC LIMIT 1";
      $this->position = ($this->conn->prepare($query,$this->page_id))+1;
      // 寫入資料庫
      $query = "INSERT INTO {$this->component_page_table} SET uuid=?, component_id=?, page_id=?, position=?, params=?";
      $this->conn->prepare($query,Array($this->uuid,$this->component_id,$this->page_id,$this->position,$this->params));
    }
    catch(Exception $e){}
  }
  
  // 更新元件與頁面的關聯
  function update() {
    echo "更新資料庫";
  }

  // 刪除元件與頁面的關聯
  function delete() {
    echo "刪除資料庫";
  }
  function createUUID(){
    return uniqid();
  }
  

}

?>