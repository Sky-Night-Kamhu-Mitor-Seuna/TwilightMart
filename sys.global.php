<?php

/*************************************************************
 * 
 * 設定值
 * 
 *************************************************************/
require_once 'sys.include.php';
session_start();
/*************************************************************/
// 版本資訊
$version = "alpha.1.0.0";
$generator = "Visual Studio Code";
$author = "snkms.com";
$design = "snkms.com";
/*************************************************************/
define("SERVER_DOMAIN_NAME", $_SERVER['SERVER_NAME']);
define("SERVER_MD5", hash('md5', SERVER_DOMAIN_NAME));
/*************************************************************/
$ini = parse_ini_file('configs/config.ini', true);
define("CONFIG_DB", $ini['database']);                        // 資料庫相關資訊
define("CONFIG_TABLES", $ini['tables']);                      // 資料表相關設置
define("CONFIG_GENERAL", $ini['general']);                    // 一般設置
/*************************************************************/
// Database服務
$db = new DBConnection(CONFIG_DB['dbname'], CONFIG_DB['host'], CONFIG_DB['port'], CONFIG_DB['username'], CONFIG_DB['password']);
$db->deBugMode(CONFIG_GENERAL['debug']);
/*************************************************************/
// 透過domainName取得網站資訊
$sql = "SELECT * FROM `" . CONFIG_TABLES['website'] . "` WHERE `domain` = ? LIMIT 1";
$result = $db->prepare($sql, [SERVER_DOMAIN_NAME]);
if (empty($result)) exit("ERROR DOMAIN!!");
define("WEBSITE", $result[0]);
define("WEBSITE_ID", WEBSITE['id']);
define("WEBSITE_NAME",  WEBSITE['displayname']);
define("WEBSITE_DISTRIBUTION", WEBSITE['distribution']);
define("WEBSITE_THENE_COLOR", "#" . WEBSITE['theme']);
define("WEBSITE_ICON_URL", WEBSITE['icon']);

if (!isset($_COOKIE['lang'])) setcookie("lang", "zh_TW");
if (isset($_COOKIE['lang'])) {
    $lang = $_COOKIE['lang'];
} else {
    $lang = 'zh_TW';
}
/*************************************************************/
// 藍新金流配置
$sql = "SELECT * FROM `" . CONFIG_TABLES['newebpay'] . "` WHERE `wid` = ? LIMIT 1";
$result = $db->prepare($sql, [WEBSITE['id']]);
if (empty($result)) exit("ERROR NO NEWEBPAY!!");
define("STORE_PREFIX", $result[0]['store_prefix']);                    // 訂單編號前置字元 (三個字母)
define("STORE_ID", $result[0]['store_id']);                            // 藍新提供的 MerchantID
define("STORE_HASH_KEY", $result[0]['store_hash_key']);                // 藍新提供的HashKey
define("STORE_HASH_IV", $result[0]['store_hash_iv']);                  // 藍新提供的HashIV
define("STORE_RETURN_URL", $result[0]['store_return_url']);            // 使用者付款完成時要跳轉到的頁面
define("STORE_CLIENT_BACK_URL", $result[0]['store_client_back_url']);  // [返回商店] 按鈕網址 (在付款頁面中)
define("STORE_NOTIFY_URL", $result[0]['store_notify_url']);            // 使用者付款完成時，藍新金流發送交易狀態結果的接收網址   
/*************************************************************/
// 權限
$permissions = new permissions($db);
// 紀錄
$log = new syslog($db);
// 商品
$productManage = new productManage($db);
// 頁面
$pageRouter = new pageRouter($db);
$pageManage = new pageManage($db);
// 雪花算法ID
$sf = new snowflake();
/*************************************************************/
// 用戶SESSION ID
// if (!isset($_SESSION["mid"])) 
$_SESSION['sessionId'] = $sf->getId();
/************************************************************/
// 用戶的網際協定位址及使用裝置
if (!empty($_SERVER["HTTP_CLIENT_IP"])) $USER_IP_ADDRESS = $_SERVER["HTTP_CLIENT_IP"];
elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) $USER_IP_ADDRESS = $_SERVER["HTTP_X_FORWARDED_FOR"];
else $USER_IP_ADDRESS = $_SERVER["REMOTE_ADDR"];
$USER_AGENT = (strlen($_SERVER['HTTP_USER_AGENT']) > 255) ? "unknown" : $_SERVER['HTTP_USER_AGENT'];
/*************************************************************/
// 測試用資訊
$TESTID = 589605057335390208;