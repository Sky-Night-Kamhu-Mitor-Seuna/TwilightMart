<?php
/****************************************************************************************
 * 
 * 商店元件
 * 推薦商品
 * 
****************************************************************************************/
$productImagePath="assets/uploads/{$serverMD5}/products/";

if( isset($_GET['pid']) || isset($_GET['cert']) ){
    $id = isset($_GET['pid']) ? $_GET['pid'] : $_GET['cert'];
}

$info = $db->prepare("SELECT * FROM `{$CONFIG_TABLES['products']}` WHERE `id` = ? AND `status` = 1 LIMIT 1;",[$id]);
// $smarty->assign("product",$info[0]);

$sql="SELECT p.*, ptype.name AS `types` 
FROM `{$CONFIG_TABLES['products']}` AS p JOIN `{$CONFIG_TABLES['product_types']}` AS ptype 
ON p.type_id = ptype.id 
WHERE p.id <> '{$info[0]['id']}' 
AND p.type_id = '{$info[0]['type_id']}'
ORDER BY RAND() LIMIT 4;";
$productsInfo=$db->each($sql);
// 將對應的圖片放置進去
foreach($productsInfo as $i => $value){
    $productsInfo[$i]['image_url']=file_exists("{$productImagePath}".$productsInfo[$i]['id'])?"{$productImagePath}{$productsInfo[$i]['id']}?t=".crc32(time()):"assets/images/quaso.png";
}
$smarty->assign("recProducts",$productsInfo);
?>
