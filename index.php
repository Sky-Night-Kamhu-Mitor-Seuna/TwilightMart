<?php
/*************************************************************
 *
 * lastUpdate 02/27/2023
 * 主頁
 * By MaizuRoad
 * 
*************************************************************/
// GLOBAL
require 'sys.global.php';
/************************************************************/
$smarty->assign("css", $includeCss);
$smarty->assign("js", $includeJs);
$smarty->assign("generator", $generator);
$smarty->assign("author", $author);
$smarty->assign("site_name", $siteName);
$smarty->assign("themeColor", $themeColor);
$smarty->assign("distribution", $distribution);
$smarty->assign("title", $siteName."｜".$pageTitle);
$smarty->assign("description", $pageDescription);
$smarty->assign("ogimage", $pageImageUrl);
//$smarty->assign("keywords", $keywords);           現在關鍵字對SEO沒幫助暫時停用
$smarty->assign("language", $lang);
$smarty->assign("icon", $iconUrl);
$smarty->assign("serverDomainName", $serverDomainName);
$smarty->assign("design", $design);
$smarty->assign("objDir", $webObjectsDir);
$smarty->assign("webObjects",$webObjects);
$smarty->assign("version",$version);
if(isset($_SESSION["account"])) $smarty->assign("memberAccount",$_SESSION["account"]);
/************************************************************/
// 導覽列
$smarty->assign("navbarList",$navbarList);
/************************************************************/
// 使用者資訊
if(isset($_SESSION["account"])) $smarty->assign("loggedin",1);
else setcookie("cart",null,time()-86400);
// else setcookie("loggedin",0,time()+60*60*24*30);
/************************************************************/
// 測試
if($debug){
    $aaa=new componentPage($db);
    $debugInfo.="<!-- ".$aaa->createUUID()." -->";
    $smarty->assign("debugInfo",$debugInfo);
    //echo session_save_path();
    //echo print_r($webObjects);
    echo "$_POST: ".var_dump($_POST)."\n<br/>";
    echo "$_SESSION: ".var_dump($_SESSION)."\n<br/>";
}
/************************************************************/
// 開始運作
$smarty->setTemplateDir($templatesDir);
$smarty->display($template);
/************************************************************/