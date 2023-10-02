<?php
/****************************************************************************************
 * 
 * 商店元件
 * 
 * 
****************************************************************************************/
$includeJs[]="./javascripts/productSort.js";
$productImagePath="assets/uploads/{$serverMD5}/products/";
// 預設值
$smarty->assign("type","所有商品");
$smarty->assign("typeDesc","我們最優質的商品一覽");

// 印出商品(指定分類)
if(isset($_GET['type'])){
    $searchType = htmlspecialchars(trim($_GET['type']));
    $sql="SELECT p.*, ptype.name AS `type`, ptype.description AS `type_desc`
    FROM `{$CONFIG_TABLES['products']}` AS p JOIN `{$CONFIG_TABLES['product_types']}` AS ptype 
    ON p.type_id = ptype.id WHERE p.type_id = ? AND p.status = 1 ;";
    $productsInfo=$db->prepare($sql,[$searchType]);
    if(!empty($productsInfo[0]['name'])){
        $smarty->assign("type",$productsInfo[0]['type']);
        $smarty->assign("typeDesc",$productsInfo[0]['type_desc']);
    }
}
// 印出商品(沒有指定)
else{
    $sql="SELECT p.*, ptype.name AS `type` 
    FROM `{$CONFIG_TABLES['products']}` AS p JOIN `{$CONFIG_TABLES['product_types']}` AS ptype 
    ON p.type_id = ptype.id WHERE p.status = 1;";
    $productsInfo=$db->each($sql);
}

// 將對應的圖片放置進去
foreach($productsInfo as $i => $value){
    $productsInfo[$i]['image_url']=file_exists("{$productImagePath}".$productsInfo[$i]['id'])?"{$productImagePath}{$productsInfo[$i]['id']}?t=".crc32(time()):"assets/images/quaso.png";
}
$smarty->assign("products",$productsInfo);
?>