<?php
/****************************************************************************************
 * 
 * 公告欄元件
 * 
 * 
****************************************************************************************/;
$includeJs[]="./javascripts/announcementSwitch.js";
$announcement = array(
    "公告"=>"1這裡沒有任何訊息但之後會有一些訊息<br/>測試垮鬆<code>".generateCRC32("未分類")."</code>",
    "促銷"=>"2這裡沒有任何訊息但之後會有一些訊息<br/>",
    "系統"=>"3這裡沒有任何訊息但之後會有一些訊息<br/>",
    "TEST"=>generateCRC32()
);
$smarty->assign("announcement",$announcement);
?>