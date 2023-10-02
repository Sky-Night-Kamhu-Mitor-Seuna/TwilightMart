<?php
/*************************************************************
 * 
 * lastUpdate 02/27/2023
 * 設定值
 * By MaizuRoad
 * 
*************************************************************/
require_once 'sys.include.php';
// require_once 'php/class.uploadImage.php';
//foreach (glob(__DIR__ . '/class.*.php') as $filename) require_once $filename;
session_start();
ob_start();
/*************************************************************/
// 版本資訊
$version="alpha.1.0.0";
/*************************************************************/
$ini=parse_ini_file('./configs/config.ini',true);
$CONFIG_DB = $ini['database'];
$CONFIG_TABLES = $ini['tables'];
$CONFIG_GENERAL = $ini['general'];
$CONFIG_WEB_DBNAME = $CONFIG_DB['dbname'];
/*************************************************************/
// Smarty配置
$smarty = new Smarty;
$smarty->caching = $CONFIG_GENERAL['smarty_caching'];
$smarty->cache_lifetime = $CONFIG_GENERAL['smarty_cache_lifetime'];
$smarty->force_compile = $CONFIG_GENERAL['debug'];
$smarty->debugging = $CONFIG_GENERAL['debug'];
/*************************************************************/
// Database服務
$db = new DBConnection($CONFIG_WEB_DBNAME,$CONFIG_DB['host'],$CONFIG_DB['port'],$CONFIG_DB['username'],$CONFIG_DB['password']);
$db->deBugMode($CONFIG_GENERAL['debug']);
/*************************************************************/
// 查詢一些伺服器基本配置
$serverDomainName=$_SERVER['SERVER_NAME'];
$serverMD5=hash('md5',$serverDomainName);
$sql = "SELECT * FROM {$CONFIG_TABLES['website']} WHERE `domain` = ? LIMIT 1";
$WEBSITE = $db->prepare($sql,[$serverDomainName])[0];
if(!$WEBSITE['domain']){ echo"ERROR DOMAIN!!";exit(); }
/*************************************************************/
// 藍新金流配置
$sql = "SELECT * FROM {$CONFIG_TABLES['newebpay']}  WHERE `id` = ? LIMIT 1";
$NEWEBPAY = $db->prepare($sql,[$WEBSITE['id']])[0];
if(!$NEWEBPAY['id']){ echo"ERROR NO NEWEBPAY!!";exit(); }
$STORE_PREFIX = $NEWEBPAY['store_prefix'];                    // 訂單編號前置字元 (三個字母)
$STORE_ID = $NEWEBPAY['store_id'];                            // 藍新提供的 MerchantID
$STORE_HASH_KEY = $NEWEBPAY['store_hash_key'];                // 藍新提供的HashKey
$STORE_HASH_IV = $NEWEBPAY['store_hash_iv'];                  // 藍新提供的HashIV
$STORE_RETURN_URL = $NEWEBPAY['store_return_url'];            // 使用者付款完成時要跳轉到的頁面
$STORE_CLIENT_BACK_URL = $NEWEBPAY['store_client_back_url'];  // [返回商店] 按鈕網址 (在付款頁面中)
$STORE_NOTIFY_URL = $NEWEBPAY['store_notify_url'];            // 使用者付款完成時，藍新金流發送交易狀態結果的接收網址
/*************************************************************/
// 重設資料庫
$db->resetDBname($WEBSITE['dbname']);
/*************************************************************/
// 資源
$template = 'main.htm';
$templatesDir = "templates/".$WEBSITE['stylesheet'];
$defaultCss=array(
    "./css/".$WEBSITE['stylesheet']."/stylesheet.css",
    "./css/".$WEBSITE['stylesheet']."/".$WEBSITE['theme'].".css"
);
$defaultJs=array(
    "./javascripts/jquery-3.2.1.min.js",
    "./javascripts/jquery-3.3.1.slim.min.js",
    "./javascripts/bootstrap.min.js",
    "./javascripts/popper.min.js",
    "./javascripts/change.js",
    "./javascripts/biscuit.js",
    "./javascripts/cart.js"
);
$includeCss=$defaultCss;
$includeJs=$defaultJs;
/*************************************************************/
// 作者
$generator="Visual Studio Code";
$author="snkms.com";
$design="snkms.com";
/*************************************************************/
// 語言及伺服器配置
$siteName=$WEBSITE['displayname'];
$distribution=$WEBSITE['distribution'];
$themeColor="#".$WEBSITE['theme'];
$iconUrl=$WEBSITE['icon'];
if(!isset($_COOKIE['lang'])) setcookie("lang","zh_TW");
$lang=$_COOKIE['lang'];
/*************************************************************/
// 導覽列定義
$sql = "SELECT `displayname`,`link` FROM {$CONFIG_TABLES['navbar']};";
foreach ( $db->each($sql) as $key => $values )
{ $navbarList[$values['displayname']]=$values['link']; }
/*************************************************************/
// 頁面設置
$pagename = isset($_GET['page']) ? $_GET['page']:"home";
$setPage=new pageRouter($db);
$setPage->setTables($CONFIG_TABLES['component_page'],$CONFIG_TABLES['pages']);
$pageInfo=$setPage->getPageInfo($pagename)[0];
if($pageInfo == null){
    $pagename="err";
    $pageInfo=$setPage->getPageInfo($pagename)[0];
}
$pageTitle=$pageInfo['title'];
$pageDescription=$pageInfo['description'];
$pageImageUrl=$pageInfo['image'];
$memberPermissions=new permissions($db);
$memberPermissions->setTables($CONFIG_TABLES['member_roles'],$CONFIG_TABLES['role_permissions'],$CONFIG_TABLES['roles']);
//$keywords=array("關鍵字","另一個關鍵字");     //現在關鍵字對SEO沒幫助暫時停用
/*************************************************************/
// 網站使用物件
$webObjects=$setPage->getComponentPage($pagename);
$debugInfo="";
//引入php
foreach ($webObjects as $key => $webObject){
    if (file_exists("php/component_{$webObject['component_id']}.php") 
    && include_once "php/component_{$webObject['component_id']}.php") {
        $debugInfo.="<!--[php/component_{$webObject['component_id']}.php]({$webObject['displayname']})-->\n";
    }
}
/*************************************************************/

?>