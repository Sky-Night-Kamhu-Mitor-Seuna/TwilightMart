<?php
class files{
    private $folderPath;
    private $debugmode=false;
    private $permission = 0775;
    /************************************************
     * ### files ###
     * @param string $folderPath 檔案上傳之目錄  
     * 注意目錄不存在將會自動建立
     ************************************************/
    public function __construct($folderPath="null"){
        $this->setPath($folderPath);
    }
    /************************************************
     * ### 設置目錄位址 ###
     * @param string $folderPath 檔案上傳之目錄  
     * 注意目錄不存在將會自動建立
     ************************************************/
    public function setPath($folderPath="null"){
        try{
            $this->folderPath = "../../assets/uploads/".$folderPath;
            if (!is_dir($this->folderPath)) {
                mkdir($this->folderPath , $this->permission, true);
            }
        }catch(Exception $e){
            if($this->debugmode) exit($e);
            else exit("Error: Upload directory error.");
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
    public function upload($file,$fileRename=null){
        try{
            if(!$fileRename) $fileRename=$file['name'];
            $targetPath = $this->folderPath."/{$fileRename}";//.pathinfo($file['name'],PATHINFO_EXTENSION);
            if(move_uploaded_file($file['tmp_name'], $targetPath)) return $targetPath; //return str_replace("../..","",$targetPath)."?t=".crc32(time());
            else return false;
        }catch(Exception $e){
            if($this->debugmode) exit($e);
            else exit("Error: Upload {$file} error.");
        }
    }
    //$file['error'] === UPLOAD_ERR_OK
    /************************************************
     * ### 設置debug模式 ###
     * @param bool $isdebug 設置debug模式
     ************************************************/
    public function deBugMode($isdebug=true){
        $this->debugmode=$isdebug;
    }
    
}
?>