<?php
/****************************************************************************************
 * 
 * 登出系統元件
 * 
 * 
****************************************************************************************/
    session_start(); 
    $_SESSION = array(); 
    session_destroy(); 
    //if(isset($row['nickname'])){setcookie("nickname", $row['nickname'], time()-604801,"/");}
    echo "<!--跳轉頁面中...-->";
    header("location: /"); 
    exit;
 ?>