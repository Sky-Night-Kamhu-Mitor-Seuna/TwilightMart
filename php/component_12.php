<?php
error_reporting(0);
date_default_timezone_set('Asia/Taipei');
if($_SERVER['HTTP_USER_AGENT'] != "pay2go"){
	http_response_code(401);
	exit;
}
require_once('../p/NewebPay/neweb.allinone.sdk.php');
			
$obj = new NewebPay_MPG_API();
$obj->HashKey = $STORE_HASH_KEY;
$obj->HashIV = $STORE_HASH_IV;
$data_raw = $obj->create_aes_decrypt($_POST['TradeInfo'],$obj->HashKey,$obj->HashIV);

if(!$data_raw){http_response_code(401);exit;}

$data = json_decode($data_raw,true);

if($_POST['Status'] == "SUCCESS" && $_POST['MerchantID'] && $_POST['TradeInfo']){
	// 成功付款時執行的操作
	// 獲取訂單買家
	$buyer = $db->prepare("SELECT member_id FROM `{$CONFIG_TABLES['orders']}` WHERE id = ? LIMIT 1;",[$data['Result']['MerchantOrderNo']])[0]['member_id'];
	
	
	
	/*
	 *
	 * 對其他資料庫的操作可以寫在這中間
	 *
	 *
	*/
	
	
	
	// 更新狀態碼
	$db->prepare("UPDATE `{$CONFIG_TABLES['orders']}` SET `status` = '1|OK' WHERE id = ?;",[$data['Result']['MerchantOrderNo']]);
	
	echo '1|OK';
}
else{
	http_response_code(404);
	$db->prepare("UPDATE `{$CONFIG_TABLES['orders']}` SET `status` = '0|NO' WHERE id = ?;",[$data['Result']['MerchantOrderNo']]);
	
	echo '0|NO';
}
?>