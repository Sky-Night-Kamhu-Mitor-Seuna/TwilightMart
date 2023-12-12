<?php

/*************************************************************
 * 
 * 檔案處理系統
 * @param obj $db 資料庫
 * @param string $folderPath 檔案上傳之目錄  
 * 注意目錄不存在將會自動建立
 *************************************************************/
class files
{
    private $folderPath;
    private $permission = 0775;
    public function __construct($folderPath = "unknown")
    {
        $this->setPath($folderPath);
    }
    /************************************************
     * ### 設置目錄位址 ###
     * @param string $folderPath 檔案上傳之目錄  
     * 注意目錄不存在將會自動建立
     ************************************************/
    public function setPath($folderPath = "unknown"): bool
    {
        $this->folderPath = "../assets/uploads/{$folderPath}";
        if (!is_dir($this->folderPath)) {
            if (!@mkdir($this->folderPath, $this->permission, true)) {
                return false;
            }
        }
        return true;
    }
    /************************************************
     * ### 取得目錄位址 ###
     ************************************************/
    public function getPath()
    {
        return $this->folderPath;
    }
    /************************************************
     * ### 上傳檔案 ###
     * @param $_FILE $file 檔案  
     * @param string $fileRename 檔案重新命名（留空則依照檔案本名）
     ************************************************/
    public function upload($file, $fileRename = null): string
    {
        if (is_null($fileRename)) $fileRename = $file['name'];
        else {
            $fileExtension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileRename .= '.' . $fileExtension;
        }
        try {
            $targetPath = "{$this->folderPath}/{$fileRename}";
            if (move_uploaded_file($file['tmp_name'], $targetPath)) return $targetPath;
            else return $file['tmp_name'];
        } catch (Exception $e) {
            return $e;
        }
    }
}
