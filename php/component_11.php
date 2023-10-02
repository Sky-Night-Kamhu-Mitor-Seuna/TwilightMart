<?php
error_reporting(0);
header('Content-Type: application/json; charset=utf-8');
$cert= $_POST['cert'];
if($_POST['submit'] && $_SESSION['mid'] && preg_match("/^([0-9a-zA-Z]{8})$/i",$_POST['cert'])){

	require_once('api/NewebPay/neweb.allinone.sdk.php');

    $sql="SELECT `price`, `name`
    FROM `{$CONFIG_TABLES['products']}` 
    WHERE `id` = ?;";
	$info = $db->prepare($sql,[$cert]);

	if($info[0] <= 0){
		http_response_code(400);
		exit(json_encode(array("status"=>400,"data"=>"Invalid price variable")));
	}

	$obj = new NewebPay_MPG_API();
				
	//服務參數
	$obj->ServiceURL  = "https://core.newebpay.com/MPG/mpg_gateway"; //API服務位置
	$obj->ReturnURL  = $STORE_RETURN_URL; //支付完成，返回商店網址
	$obj->NotifyURL  = $STORE_NOTIFY_URL; //支付通知網址
	$obj->CustomerURL  = "";          //商店取號網址
	$obj->ClientBackURL  = $STORE_CLIENT_BACK_URL; //返回商店網址
		
	$obj->HashKey     = $STORE_HASH_KEY ; //Hashkey，請自行帶入藍新提供的HashKey
	$obj->HashIV      = $STORE_HASH_IV ; //HashIV，請自行帶入藍新提供的HashIV
	$obj->MerchantID  = $STORE_ID;  //MerchantID，請自行帶入藍新提供的MerchantID
	$obj->MerchantPrefix     = $STORE_PREFIX;
	$obj->MerchantTradeNo = $obj->getOrderNo();
	$obj->Version     		= '1.6';
				
	$obj->Amount = $info[0];
	$obj->Order_Title = $info[1];
	$obj->ExpireDate = 3; // 訂單時效
	$obj->DisableATM = false; // 是否關閉ATM付款
	
	$hash = md5($obj->MerchantTradeNo.$info[0]['price']);
				
	

	$sql="INSERT INTO `{$CONFIG_TABLES['orders']}` 
	(`id`, `member_id`, `amount`, `status`, `hash`) 
	VALUES (?, ?, ?, ?, ?)";
	$db->prepare($sql,[$obj->MerchantTradeNo, $_SESSION['mid'], $info[0]['price'], '0|NO', $hash]);
	
	$sql="INSERT INTO `{$CONFIG_TABLES['order_items']}` 
	(`order_id`, `product_id`, `quantity`, `price`) 
	VALUES (?, ?, ?, ?)";
	$db->prepare($sql,[$obj->MerchantTradeNo, $cert, 1, $info[0]['price']]);
				
	exit($obj->getOutputJSON());
}
else{
	http_response_code(401);
	//exit(json_encode(array("status"=>401,"data"=>"Invalid session")).var_dump($_POST).var_dump($_SESSION));
	
}

?>