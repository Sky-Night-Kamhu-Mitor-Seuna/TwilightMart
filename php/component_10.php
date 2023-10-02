<?php
/****************************************************************************************
 * 
 * 商店元件
 * 商品結帳前確認
 * 
****************************************************************************************/
$productImagePath="assets/uploads/{$serverMD5}/products/";
error_reporting(0);
// !preg_match("/^([0-9a-zA-Z]{8})$/i",$_GET['cert'])
if(!isset($_GET['cert']) || !preg_match("/^([0-9a-zA-Z]{8})$/i",$_GET['cert'])){
	header("location: ?page=store");
	exit;
}
$cert=htmlspecialchars($_GET['cert']);
// require_once ('./p/global.php');
// require_once ('./p/connectDatabase.php');

if(!$_SESSION['mid']){
	header("location: ?page=login");
	exit;
}
$productInfo = $db->prepare("SELECT * FROM `{$CONFIG_TABLES['products']}` WHERE id = ? AND `status` = 1  LIMIT 1;",[$cert]);
//未上架商品
if(!$productInfo[0]) header("location: ?page=store");
// 將對應的圖片放置進去
$productInfo[0]['image_url']=file_exists("{$productImagePath}".$productInfo[0]['id'])?"{$productImagePath}{$productInfo[0]['id']}?t=".crc32(time()):"assets/images/quaso.png";

$smarty->assign("product",$productInfo[0]);
?>