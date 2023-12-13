<?php
/****************************************************************************************
 * 
 * 商品元件
 * 
****************************************************************************************/
// 引入的CSS及JS
$includeCss[]="./css/".WEBSITE['stylesheet']."/productList.css";
// $includeJs[] = "./javascripts/.js";
/****************************************************************************************/

$products = $productManage->getProductInformation(WEBSITE_ID);
$smarty->assign("products", $products);
?>