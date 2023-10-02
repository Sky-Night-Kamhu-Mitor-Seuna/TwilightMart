<?php
	include('./neweb.allinone.sdk.php');
	$obj = new NewebPay_MPG_API();
	
	//服務參數
	$obj->ServiceURL			= "https://ccore.newebpay.com/MPG/mpg_gateway";		// API服務位置 (此為測試環境網址)
	$obj->ReturnURL				= "";												// 支付完成，返回商店網址
	$obj->NotifyURL				= "";												// 支付通知網址
	$obj->CustomerURL			= "";												// 商店取號網址
	$obj->ClientBackURL			= "";												// 返回商店網址
	
	$obj->HashKey				= '';												// Hashkey
	$obj->HashIV				= '';												// HashIV
	$obj->MerchantID			= '';												// MerchantID
	$obj->MerchantPrefix		= '';												// 訂單編號前導字元
	$obj->MerchantTradeNo		= $this->getOrderNo();								// 訂單編號
	$obj->Version				= '1.5';											// API程式版本
	
	$obj->Amount				= 0;												// 商品價格
	$obj->Order_Title			= "";												// 商品名稱
	$obj->ExpireDate			= 3;												// 付款期限
	
	$obj->getOutput();