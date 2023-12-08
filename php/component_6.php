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
$log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION["mid"], $USER_IP_ADDRESS, "LOGOUT", 1);
// unset($_SESSION['account']);
// unset($_SESSION['mid']);
$_SESSION = array();
session_destroy();
echo "<!--跳轉頁面中...-->";
header("location: /");
exit;