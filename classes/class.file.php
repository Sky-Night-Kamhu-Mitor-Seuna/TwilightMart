<?php
/*************************************************************
 * 
 * 檔案處理系統
 * @param obj $db 資料庫
 * @param string $folderPath 檔案上傳之目錄  
 * 注意目錄不存在將會自動建立
*************************************************************/
class files{
    private $folderPath;
    private $debugmode=false;
    private $permission = 0775;
    public function __construct($folderPath="null"){
        $this->setPath($folderPath);
    }
    /************************************************
     * ### 設置目錄位址 ###
     * @param string $folderPath 檔案上傳之目錄  
     * 注意目錄不存在將會自動建立
     ************************************************/
    public function setPath($folderPath="null")
    {
        $this->folderPath = "/assets/uploads/".$folderPath;
        try{
            if (!is_dir($this->folderPath)) mkdir($this->folderPath , $this->permission, true);
            return true;
        }catch(Exception $e){
            return false;
        }
    }
    /************************************************
     * ### 取得目錄位址 ###
     ************************************************/
    public function getPath(){
        return $this->folderPath;
    }
    /************************************************
     * ### 上傳檔案 ###
     * @param $_FILE $file 檔案  
     * @param string $fileRename 檔案重新命名（留空則依照檔案本名）
     * @return bool
     * @return $path 上傳完成路徑  
     * 注意請先透過 files() 配置上傳目錄
     ************************************************/
    public function upload($file, $fileRename=null)
    {
        if(is_null($fileRename)) $fileRename = $file['name'];

        $targetPath = $this->folderPath."/{$fileRename}";//.pathinfo($file['name'],PATHINFO_EXTENSION);
        if(move_uploaded_file($file['tmp_name'], $targetPath)) return $targetPath; //return str_replace("../..","",$targetPath)."?t=".crc32(time());
        else return false;
    }
    //$file['error'] === UPLOAD_ERR_OK
    
}
?>