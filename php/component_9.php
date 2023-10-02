<?php
/****************************************************************************************
 * 
 * 商店元件
 * 商品詳細頁面
 * 
****************************************************************************************/
$productImagePath="assets/uploads/{$serverMD5}/products/";
if(isset($_GET['pid'])){
    $pid=htmlspecialchars($_GET['pid']);
    // 印出商品詳細
    $sql="SELECT p.*, ptype.name AS `types` 
    FROM `{$CONFIG_TABLES['products']}` AS p JOIN `{$CONFIG_TABLES['product_types']}` AS ptype 
    ON p.type_id = ptype.id WHERE p.id = ? AND p.status = 1 ; ";
    $productInfo=$db->prepare($sql,[$pid]);
    //未上架商品
    if($info[0]['id']) header("location: ?page=store");
    // 將對應的圖片放置進去
    $productInfo[0]['image_url']=file_exists("{$productImagePath}".$productInfo[0]['id'])?"{$productImagePath}{$productInfo[0]['id']}?t=".crc32(time()):"assets/images/quaso.png";
    $smarty->assign("product",$productInfo[0]);
}else{
    echo "正在跳轉";
    header("location: ?page=store");
}
?>