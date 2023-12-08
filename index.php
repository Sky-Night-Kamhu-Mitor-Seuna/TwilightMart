<?php

/*************************************************************
 *
 * 主頁
 * 
 *************************************************************/
// GLOBAL
require 'sys.global.php';
/*************************************************************/
// Smarty配置
ob_start();
require_once 'libs/smarty/Smarty.class.php';
$smarty = new Smarty;
$smarty->caching = CONFIG_GENERAL['smarty_caching'];
$smarty->cache_lifetime = CONFIG_GENERAL['smarty_cache_lifetime'];
$smarty->force_compile = CONFIG_GENERAL['debug'];
$smarty->debugging = CONFIG_GENERAL['debug'] && false;
/*************************************************************/
// 資源
$template = 'main.htm';
$templatesDir = "templates/" . WEBSITE['stylesheet'];
$defaultCss = array(
    "./css/" . WEBSITE['stylesheet'] . "/stylesheet.css",
    "./css/" . WEBSITE['stylesheet'] . "/color.css",
    "./css/" . WEBSITE['stylesheet'] . "/scrollbar .css"
);
$defaultJs = array(
    // "./javascripts/jquery-3.2.1.min.js",
    // "./javascripts/jquery-3.3.1.slim.min.js",
    "./javascripts/bootstrap.min.js",
    "./javascripts/checkView.js",
    "./javascripts/popper.min.js",
    "./javascripts/change.js",
    "./javascripts/biscuit.js",
    "./javascripts/cart.js"
);
$includeCss = $defaultCss;
$includeJs = $defaultJs;
/*************************************************************/
// 頁面跳轉(之後補)
if (isset($_GET['jump'])) header("Location: {$_GET['jump']}");
/*************************************************************/
// 頁面確認
$pageName = isset($_GET['route']) ? $_GET['route'] : "home";
// $pageName = $pageName == "homepage" ?  "home" : $_GET['route'];

$pageInfo = $pageRouter->getPageInfomation($pageName, WEBSITE_ID);
if (empty($pageInfo)) {
    $pageName = "err";
    $pageInfo = $pageRouter->getPageInfomation($pageName, WEBSITE_ID);
}
$pageInfo = $pageInfo[0];
$pageTitle = $pageInfo['displayname'];
$pageDescription = $pageInfo['description'];
$pageId = $pageInfo['id'];
// $pageImageUrl = $pageInfo['icon'];
// $keywords=array("關鍵字","另一個關鍵字");     //現在關鍵字對SEO沒幫助暫時停用
/*************************************************************/
// 紀錄訪問頁面資訊
$timeDiff = 0;
$referrerUrl = isset($_GET['ref']) ? $_GET['ref'] : SERVER_DOMAIN_NAME;
$viewUser = isset($_SESSION['mid']) ? $_SESSION['mid'] : $_SESSION['sessionId'];
if (isset($_SESSION['LastVisitedPageData'])) {
    // $LastVisitedPageData = json_decode($_COOKIE['LastVisitedPageData'], true);
    $LastVisitedPageData = $_SESSION['LastVisitedPageData'];
    $lastVisitedTimestamp = intval($LastVisitedPageData['timestamp']);
    $lastVisitedPageId = intval($LastVisitedPageData['pageId']);
    $timeDiff = ($lastVisitedPageId != $pageId) ? (int)(microtime(true)) - $lastVisitedTimestamp : 0;
}
// if (isset($_COOKIE['LastVisitedPageData'])) {
//     $LastVisitedPageData = json_decode($_COOKIE['LastVisitedPageData'], true);
//     $lastVisitedTimestamp = intval($LastVisitedPageData['timestamp']);
//     $lastVisitedPageId = intval($LastVisitedPageData['pageId']);
//     $timeDiff = ($lastVisitedPageId != $pageId) ? (int)(microtime(true)) - $lastVisitedTimestamp : 0;
// }
if ($timeDiff > 0)
    $log->addViewLog($sf->getId(), $viewUser, $pageId, $USER_IP_ADDRESS, $USER_AGENT, $timeDiff, $referrerUrl);
/*************************************************************/
// 網站使用物件
$componentIncludePHPList = array();
$componentTemplateList = array();
foreach ($pageRouter->getPageComponent($pageId) as $key => $webObject) {
    if (!in_array($webObject['cid'], $componentIncludePHPList)) $componentIncludePHPList[] = $webObject['cid'];
    $componentTemplateList[$key] = ["id" => $webObject['id'], "displayname" => $webObject['displayname'], "cid" => $webObject['cid'], "param" => json_decode($webObject['params'], true)];
}
// 將元件資訊寫進smartyAssign
$smarty->assign("pageComponents", $componentTemplateList);
// 引入相關PHP
foreach ($componentIncludePHPList as $id) include_once "php/component_{$id}.php";
/*************************************************************/
$smarty->assign("css", $includeCss);
$smarty->assign("js", $includeJs);
$smarty->assign("generator", $generator);
$smarty->assign("author", $author);
$smarty->assign("siteName", WEBSITE_NAME);
$smarty->assign("themeColor", WEBSITE_THENE_COLOR);
$smarty->assign("distribution", WEBSITE_DISTRIBUTION);
$smarty->assign("title", WEBSITE['displayname'] . "｜" . $pageTitle);
$smarty->assign("description", $pageDescription);
// $smarty->assign("ogimage", $pageImageUrl);
// $smarty->assign("keywords", $keywords);           現在關鍵字對SEO沒幫助暫時停用
$smarty->assign("language", $lang);
$smarty->assign("icon", WEBSITE_ICON_URL);
$smarty->assign("serverDomainName", SERVER_DOMAIN_NAME);
$smarty->assign("design", $design);
// $smarty->assign("objDir", $webObjectsDir);
// $smarty->assign("componentTemplateIdList",$componentTemplateIdList);
$smarty->assign("version", $version);
/************************************************************/
// 使用者資訊
if (isset($_SESSION["mid"])) $smarty->assign("mid", $_SESSION["mid"]);
else setcookie("cart", null, time() - 86400);
/************************************************************/
// 設置最後一次訪問頁面
$_SESSION['LastVisitedPageData'] = ["pageId" => $pageId, "timestamp" => (int)(microtime(true))];
// setcookie('LastVisitedPageData', json_encode(['timestamp' => (int)(microtime(true)), 'pageId' => $pageId]));
/************************************************************/
// 測試
$randMessage = array("偷偷在這寫一些東西，應該不會有人看到", "這個訊息是隨機的喔", "今天早餐吃蛋餅", "我們致力讓明天更加美好", "我們會更努力");
$smarty->assign("randMessage", $randMessage[rand(0, count($randMessage) - 1)]);
/************************************************************/
// 開始運作
$smarty->setTemplateDir($templatesDir);
$smarty->display($template);
/************************************************************/
