<?php

class NewebPay_MPG_API{
	public $MerchantID;
	public $HashKey;
	public $HashIV;
	public $ServiceURL;
	public $ReturnURL;
	public $NotifyURL;
	public $CustomerURL;
	public $ClientBackURL;
	public $Order_Title;
	public $Amount;
	public $Version;
	public $ExpireDate;
	public $MerchantTradeNo;
	public $MerchantPrefix;
	public $DisableATM;
	
	public function getOutput(){
		/* 送給藍新資料 */
		$trade_info_arr = array(
			'MerchantID' => $this->MerchantID, //商店代號
			'RespondType' => 'JSON', //使用JSON格式
			'TimeStamp' => time(), //時間戳
			'Version' => $this->Version, //程式版本
			'MerchantOrderNo' => $this->MerchantTradeNo, //商品編號
			'Amt' => $this->Amount, //商品價格
			'ItemDesc' => $this->Order_Title, //商品名稱
			'ReturnURL' => $this->ReturnURL, //支付完成 返回商店網址
			'NotifyURL' => $this->NotifyURL, //支付通知網址
			'CustomerURL' =>$this->CustomerURL, //商店取號網址
			'ClientBackURL' => $this->ClientBackURL , //支付取消 返回商店網址
			'ExpireDate' => date("Y-m-d" , time()+86400*$this->ExpireDate), //繳費期限
			'VACC' => 1, // 使用 ATM轉帳
			'CVS' => 1 // 使用CVS付款
		);
		
		if($this->DisableATM) $trade_info_arr['CVS'] = 0;
		
		$TradeInfo = $this->create_mpg_aes_encrypt($trade_info_arr, $this->HashKey, $this->HashIV);
		$SHA256 = strtoupper(hash("sha256", $this->SHA256($this->HashKey,$TradeInfo,$this->HashIV)));
		
		echo $this->createForm($this->ServiceURL,$this->MerchantID,$TradeInfo,$SHA256,$this->Version);
	}
	
	public function getOutputJSON(){
		/* 送給藍新資料 */
		$trade_info_arr = array(
			'MerchantID' => $this->MerchantID, //商店代號
			'RespondType' => 'JSON', //使用JSON格式
			'TimeStamp' => time(), //時間戳
			'Version' => $this->Version, //程式版本
			'MerchantOrderNo' => $this->MerchantTradeNo, //商品編號
			'Amt' => $this->Amount, //商品價格
			'ItemDesc' => $this->Order_Title, //商品名稱
			'ReturnURL' => $this->ReturnURL, //支付完成 返回商店網址
			'NotifyURL' => $this->NotifyURL, //支付通知網址
			'CustomerURL' =>$this->CustomerURL, //商店取號網址
			'ClientBackURL' => $this->ClientBackURL , //支付取消 返回商店網址
			'ExpireDate' => date("Y-m-d" , time()+86400*$this->ExpireDate), //繳費期限
			'VACC' => 1, // 使用 ATM轉帳
			'CVS' => 1 // 使用CVS付款
		);
		
		if($this->DisableATM) $trade_info_arr['CVS'] = 0;
		
		$TradeInfo = $this->create_mpg_aes_encrypt($trade_info_arr, $this->HashKey, $this->HashIV);
		$SHA256 = strtoupper(hash("sha256", $this->SHA256($this->HashKey,$TradeInfo,$this->HashIV)));
		
		
		$result = array(
			"name"=>"newebpay",
			"url"=>$this->ServiceURL,
			"id"=>$this->MerchantID,
			"items"=>$TradeInfo,
			"sha"=>$SHA256,
			"version"=>$this->Version,
		);
		
		echo json_encode($result);
	}
	
	/*HashKey AES 加解密 */
	function create_mpg_aes_encrypt ($parameter = null , $key = "", $iv = "") {
		$return_str = '';
		if (!empty($parameter)) {
			//將參數經過 URL ENCODED QUERY STRING
			$return_str = http_build_query($parameter);
		}
		return trim(bin2hex(openssl_encrypt($this->addpadding($return_str), 'aes-256-cbc', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv)));
	}
	
	function addpadding($string, $blocksize = 32) {
		$len = strlen($string);
		$pad = $blocksize - ($len % $blocksize);
		$string .= str_repeat(chr($pad), $pad);

		return $string;
	}

	/*HashKey AES 解密 */
	function create_aes_decrypt($parameter = "", $key = "", $iv = "") {
		return $this->strippadding(openssl_decrypt(hex2bin($parameter),'AES-256-CBC', $key, OPENSSL_RAW_DATA|OPENSSL_ZERO_PADDING, $iv));
	}

	function strippadding($string) {
		$slast = ord(substr($string, -1));
		$slastc = chr($slast);
		$pcheck = substr($string, -$slast);
		if (preg_match("/$slastc{" . $slast . "}/", $string)) {
			$string = substr($string, 0, strlen($string) - $slast);
			return $string;
		} else {
			return false;
		}
	}

	/*HashIV SHA256 加密*/
	function SHA256($key="", $tradeinfo="", $iv=""){
		$HashIV_Key = "HashKey=".$key."&".$tradeinfo."&HashIV=".$iv;

		return $HashIV_Key;
	}

	/*建立表單*/
	function createForm($ServiceURL="", $MerchantID="", $TradeInfo="", $SHA256="", $VER="") {
		$szHtml = '<!doctype html>';
		$szHtml .='<html>';
		$szHtml .='<head>';
		$szHtml .='<meta charset="utf-8">';
		$szHtml .='</head>';
		$szHtml .='<body>';
		$szHtml .='<form name="newebpay" id="newebpay" method="post" action="'.$ServiceURL.'" style="display:none;">';
		$szHtml .='<input type="text" name="MerchantID" value="'.$MerchantID.'" type="hidden">';
		$szHtml .='<input type="text" name="TradeInfo" value="'.$TradeInfo.'"   type="hidden">';
		$szHtml .='<input type="text" name="TradeSha" value="'.$SHA256.'" type="hidden">';
		$szHtml .='<input type="text" name="Version"  value="'.$VER.'" type="hidden">';
		$szHtml .='</form>';
		$szHtml .='<script type="text/javascript">';
		$szHtml .='document.getElementById("newebpay").submit();';
		$szHtml .='</script>';
		$szHtml .='</body>';
		$szHtml .='</html>';

		return $szHtml;
	}
	
	/*取得訂單編號*/
	function getOrderNo(){
		date_default_timezone_set('Asia/Taipei'); // CDT
		$info = time();
		$ordre_no = $this->MerchantPrefix.time().mt_rand(111,999);

		return $ordre_no;
	}
}