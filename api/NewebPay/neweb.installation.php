<?php
/****************************************************************************************
 * 
 * 藍新金流初始化
 * 
 ****************************************************************************************/
require_once "../sys.global.php";
/****************************************************************************************/
// 藍新支付相關資訊
require_once('neweb.allinone.sdk.php');
$newPaySetting = [
    "hashKey" => STORE_HASH_KEY,
    "hashIV" => STORE_HASH_IV,
    "merchantId" => STORE_ID,
    // "merchantPrefix" => STORE_PREFIX,
    "returnURL" => STORE_RETURN_URL,
    "notifyURL" => STORE_NOTIFY_URL,
    "clientBackURL" => STORE_CLIENT_BACK_URL,
    "respondType" => "String"
];
$newebPayObj = new NewebPay_MPG_API($newPaySetting);
$newebPayObj->deBugMode(CONFIG_GENERAL['debug']);