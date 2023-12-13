<?php
/****************************************************************************************
 * 
 * 管理員頁面
 * 
****************************************************************************************/
// 引入的CSS及JS
$includeCss[]="./css/".WEBSITE['stylesheet']."/announcement.css";
$includeJs[] = "./javascripts/announcementSwitch.js";
/****************************************************************************************/
// 沒有登入
if (!isset($_SESSION['mid'])) {
    header("location: ?route=login");
    exit;
}
// 不具有任何管理員權限
if (!$permissions->isAdmin($_SESSION['mid'], WEBSITE_ID)) {
    header("location: ?route=member");
    exit;
}

$announcement = array(
    "公告"=>"如果你看到這行文字代表你具有管理員權限，必須具有對應的權限才能進行操作",
    "說明"=>"網站管理: 一些關於整個網站的操作<br/>商品管理: 跟商品有關的操作<br/>群組管理: 跟權限有關的操作",
    "版本資訊"=>"當前版本: {$version}<br/>作者: {$author}<br/>設計師: {$design}"
);
$smarty->assign("announcement",$announcement);
if(isset($_GET['noPermission'])) $smarty->assign("noPermission", $_GET['noPermission']);
?>
