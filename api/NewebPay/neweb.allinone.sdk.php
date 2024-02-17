<?php
// include "./class.connectDatabase.php";
/****************************************************************************************
 * 
 * 藍新支付串接
 * Version: 2.0.0.0 初版 By 5026.snkms 再版 By MaizuRoad.snkms
 * @param obj $setting 一些配置資料
 * 
 ****************************************************************************************/
class NewebPay_MPG_API
{
	private $debugmode = false;												// 除錯模式
	private $version = "1.6";												// API程式版本
	private $merchantId = "";												// MerchantID
	private $hashKey = "";													// Hashkey
	private $hashIV = "";													// HashIV
	private $minimumPrice = 35;												// 最低容許交易金額
	private $respondType = "JSON";											// 接收藍新返回格式
	private $serviceURL = "https://ccore.newebpay.com/MPG/mpg_gateway";		// API服務位置 
	private $returnURL = "";												// 支付完成，返回商店網址
	private $notifyURL = "../api/newebpay/api.done.php";					// 支付通知網址
	private $customerURL = "";												// 商店取號網址
	private $clientBackURL = "";											// 返回商店網址
	private $expireDate = 3;												// 付款期限
	private $disableATM = false;											// 是否關閉ATM付款
	// private $merchantPrefix;												// 訂單編號前導字元
	private $merchantTradeNo;												// 訂單編號
	private $orderTitle;													// 訂單名稱
	private $amount;														// 訂單價格

	public function __construct($setting)
	{
		$this->hashKey = $setting['hashKey'];
		$this->hashIV = $setting['hashIV'];
		$this->merchantId = $setting['merchantId'];
		// $this->merchantPrefix = strtoupper($setting['merchantPrefix']);

		if (isset($setting['respondType'])) $this->respondType = $setting['respondType'];
		if (isset($setting['returnURL'])) $this->returnURL = $setting['returnURL'];
		if (isset($setting['notifyURL'])) $this->notifyURL = $setting['notifyURL'];
		if (isset($setting['customerURL'])) $this->customerURL = $setting['customerURL'];
		if (isset($setting['clientBackURL'])) $this->clientBackURL = $setting['clientBackURL'];
		if (isset($setting['expireDate'])) $this->expireDate = $setting['expireDate'];
		if (isset($setting['disableATM'])) $this->disableATM = $setting['disableATM'];
	}
	/************************************************
	 * ### 設置訂單資訊 ###
	 * @param string $merchantTradeNo 訂單編號
	 * @param string $orderTitle 訂單標題
	 * @param int $amount 訂單金額
	 ************************************************/
	public function settingOrderInformation($merchantTradeNo, $orderTitle, $amount = 0): bool
	{
		// $merchantTradeNo = $this->merchantPrefix . $snowflakeId;
		if ($amount <= $this->minimumPrice) return false;
		// if (!preg_match('/^[A-Z]{1,8}[0-9]{18,20}$/', $merchantTradeNo)) return false;
		$this->merchantTradeNo = $merchantTradeNo;
		$this->orderTitle = $orderTitle;
		$this->amount = (int)$amount/1;
		return true;
	}
	/************************************************
	 * ### 取得傳送至藍新陣列格式 ###
	 ************************************************/
	private function getTradeInformationArray(): array
	{
		$tradeInformationArray = [
			'MerchantID' => $this->merchantId,									// 商店代號
			'RespondType' => $this->respondType,								// 接收藍新返回格式
			'TimeStamp' => time(),												// 時間戳
			'Version' => $this->version,										// API程式版本
			'MerchantOrderNo' => $this->merchantTradeNo,						// 訂單編號
			'Amt' => $this->amount,												// 訂單價錢
			'ItemDesc' => $this->orderTitle,									// 訂單標題
			'ReturnURL' => $this->returnURL,									// 支付完成，返回商店網址
			'NotifyURL' => $this->notifyURL,									// 支付通知網址
			'CustomerURL' => $this->customerURL,								// 商店取號網址
			'ClientBackURL' => $this->clientBackURL,							// 支付取消 返回商店網址
			'ExpireDate' => date("Y-m-d", time() + 86400 * $this->expireDate),	// 繳費期限
			'VACC' => 1, 														// 使用ATM轉帳
			'CVS' => 1 															// 使用CVS付款
		];
		if ($this->disableATM) $tradeInformationArray['CVS'] = 0;
		$result["information"] = $this->create_mpg_aes_encrypt($tradeInformationArray, $this->hashKey, $this->hashIV);
		$result["sha256"] = strtoupper(hash("sha256", $this->SHA256($this->hashKey, $result["information"], $this->hashIV)));
		return $result;
	}
	/************************************************
	 * ### 輸出 ###
	 * @param int $method 格式 "JSON":單純輸出JSON、"FORM":輸出表格
	 ************************************************/
	public function getOutput($method = "JSON")
	{
		$tradeInformationArray = $this->getTradeInformationArray();
		if (strtoupper($method) == "JSON") {
			$result = json_encode(
				[
					"name" => "newebpay",
					"url" => $this->serviceURL,
					"id" => $this->merchantId,
					"items" => $tradeInformationArray['information'],
					"sha" => $tradeInformationArray['sha256'],
					"version" => $this->version
				]
			);
		} else if (strtoupper($method) == "FORM") $result = $this->createForm($tradeInformationArray['information'], $tradeInformationArray['sha256']);
		else $result = "";
		echo $result;
	}
	/************************************************
	 * ### HashKey AES 加解密 ###
	 ************************************************/
	private function create_mpg_aes_encrypt($parameter = [], $key = "", $iv = "")
	{
		$return_str = '';
		if (!empty($parameter)) {
			//將參數經過 URL ENCODED QUERY STRING
			$return_str = http_build_query($parameter);
		}
		return trim(bin2hex(openssl_encrypt($this->addpadding($return_str), 'aes-256-cbc', $key, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $iv)));
	}
	private function addpadding($string, $blocksize = 32)
	{
		$len = strlen($string);
		$pad = $blocksize - ($len % $blocksize);
		$string .= str_repeat(chr($pad), $pad);

		return $string;
	}
	/************************************************
	 * ### HashKey AES 解密 ###
	 ************************************************/
	public function create_aes_decrypt($parameter = "")
	{
		return $this->strippadding(openssl_decrypt(hex2bin($parameter), 'AES-256-CBC', $this->hashKey, OPENSSL_RAW_DATA | OPENSSL_ZERO_PADDING, $this->hashIV));
	}
	private function strippadding($string)
	{
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
	/************************************************
	 * ### HashIV SHA256 加密 ###
	 ************************************************/
	private function SHA256($tradeinfo = "")
	{
		$HashIV_Key = "HashKey=" . $this->hashKey . "&" . $tradeinfo . "&HashIV=" . $this->hashIV;

		return $HashIV_Key;
	}
	/************************************************
	 * ### 建立表單 ###
	 * @param string $tradeInformation 交易資訊
	 * @param string $sha256 交易雜湊 
	 ************************************************/
	private function createForm($tradeInformation = "", $sha256 = "", $Output2newebpay=true): string
	{
		$result  = "<!doctype html>";
		$result .= "<html>";
		$result .= "<head>";
		$result .= "<meta charset='utf-8'>";
		$result .= "</head>";
		$result .= "<body>";
		$result .= "<form name='newebpay' id='newebpay' method='post' action='{$this->serviceURL}' style=display:none;>";
		$result .= "<input type='text' name='MerchantID' value='{$this->merchantId}'>";
		$result .= "<input type='text' name='TradeInfo' value='{$tradeInformation}'>";
		$result .= "<input type='text' name='TradeSha' value='{$sha256}'>";
		$result .= "<input type='text' name='Version'  value='{$this->version}'>";
		$result .= "</form>";
		if($Output2newebpay){
			$result .= "<script type='text/javascript'>";
			$result .= "document.getElementById('newebpay').submit();";
			$result .= "</script>";
		}
		$result .= "</body>";
		$result .= "</html>";
		return $result;
	}
	/************************************************
	 * ### 設置debug模式 ###
	 * @param bool $isdebug 設置debug模式
	 ************************************************/
	public function deBugMode($isdebug = true)
	{
		$this->debugmode = $isdebug;
		$this->serviceURL = $this->debugmode ? "https://ccore.newebpay.com/MPG/mpg_gateway" : "https://core.newebpay.com/MPG/mpg_gateway";
	}
}
