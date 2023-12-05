<?php
/****************************************************************************************
 * 
 * 登出系統元件
 * 
 ****************************************************************************************/
// 引入的CSS及JS
// $includeCss[]="./css/".WEBSITE['stylesheet']."/.css";
// $includeJs[] = "./javascripts/.js";
/****************************************************************************************/
// session_start();
$log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION["mid"], $USER_IP_ADDRESS, "LOGOUT", 1);
$_SESSION = array();
session_destroy();
//if(isset($row['nickname'])){setcookie("nickname", $row['nickname'], time()-604801,"/");}
echo "<!--跳轉頁面中...-->";
header("location: /");
exit;
