<?php

/****************************************************************************************
 * 
 * 商品編輯頁面
 * 
 ****************************************************************************************/
// 引入的CSS及JS
$includeCss[] = "./css/" . WEBSITE['stylesheet'] . "/productEdit.css";
$includeJs[] = "./javascripts/updateProducts.js";
$includeJs[] = "./javascripts/uploadImage.js";
/****************************************************************************************/
// 沒有登入
if (!isset($_SESSION['mid'])) {
    header("location: ?route=login");
    exit;
}
// if(isset($_POST['productName']) && isset($_POST['productPrice']) && isset($_POST['productQuantity'])){
    
//     $productName = htmlspecialchars($_POST['productName']);
//     $productQuantity = intval($_POST['productQuantity']);
//     $productPrice = floatval($_POST['productPrice']);
//     $result=$productManage->addProduct($sf->getId(), WEBSITE_ID, $productName, $productPrice,$productQuantity);
//     // $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['mid'], $USER_IP_ADDRESS, "ADD_PRODUCT", !empty($result));
//     unset($_POST);
// }


$products = $productManage->getProductInformation(WEBSITE_ID);

$smarty->assign("products", $products);
$smarty->assign("randomProductTemporaryId", $sf->getId());
/****************************************************************************************/
