<?php
$includeJs[]="./javascripts/productEdit.js";
$productImagePath="assets/uploads/{$serverMD5}/products/";
$smarty->assign("action",$_GET['action']);
// 商品調整
if(!isset($_SESSION['mid'])){
    header("location: ?page=login");
    exit;
}
// 新增商品
if(isset($_POST['add'])){
    
    $sql="SELECT `id` FROM `{$CONFIG_TABLES['product_types']}` WHERE `name` = ?";
    $pTypeId=$db->prepare($sql,[$_POST['pType']])[0]['id'];
    $pPrice=doubleval($_POST['pPrice']);
    if($pPrice<=0){
        $smarty->assign("Failed2Add",True);
    }else{
        $id = generateCRC32($_POST['pName']);
        $sql="INSERT INTO `{$CONFIG_TABLES['products']}` (`id`,`name`,`price`,`description`,`type_id`,`status`)
        VALUES(?, ?, ?, '沒有介紹', ?, 2);";
        $db->prepare($sql,[$id,$_POST['pName'],$pPrice,"c8862d23"]);
        if(!empty($id)){
            header("location: ?page=member&action=productEdit&pid=".$id);
            exit;
        }
        $smarty->assign("Failed2Add",True);
    }  
}
// 刪除或編輯商品
if(isset($_POST['pid']) && preg_match("/^([0-9a-zA-Z]{8})$/i",$_POST['pid'])){
    $pid=$_POST['pid'];
    $sql="SELECT `id` FROM `{$CONFIG_TABLES['products']}` WHERE `id` = ? ;";
    $checkid=$db->prepare($sql,[$pid]);
    if(!empty($checkid) && $checkid[0]['id'] == $pid){
        if(isset($_POST['status'])){
            $sql="UPDATE `{$CONFIG_TABLES['products']}` SET `status`=? WHERE id = ?;";
            $db->prepare($sql,[$_POST['status'],$pid]);
        }
        if(isset($_POST['edit'])&&isset($_POST['pName'])&&isset($_POST['pDesc'])&&isset($_POST['pPrice'])){
            $price=doubleval($_POST['pPrice']);
            $sql="UPDATE `{$CONFIG_TABLES['products']}` SET `name`=?, `price`=?, `description`=? WHERE id = ?;";
            $db->prepare($sql,[$_POST['pName'],$price,$_POST['pDesc'],$pid]);
        }
    }
    // echo "PID IS SETED";
    // exit;
}
// 商品詳細頁面調整
if(isset($_GET['pid']) && preg_match("/^([0-9a-zA-Z]{8})$/i",$_GET['pid'])){
    $smarty->assign("setPid",isset($_GET['pid']));
    $pid=htmlspecialchars($_GET['pid']);
    // 印出商品詳細
    $sql="SELECT p.*, ptype.name AS `types` 
    FROM `{$CONFIG_TABLES['products']}` AS p 
    JOIN `{$CONFIG_TABLES['product_types']}` AS ptype 
    ON p.type_id = ptype.id 
    WHERE p.id = ? AND p.status IN (1,2) ; ";
    $productInfo=$db->prepare($sql,[$pid]);
    // 未上架商品
    // if($info[0]['id']) header("location: ?page=member&action=productEdit");
    $productInfo[0]['image_url']=file_exists("{$productImagePath}".$productInfo[0]['id'])?"{$productImagePath}{$productInfo[0]['id']}?t=".crc32(time()):"assets/images/quaso.png";
    $smarty->assign("product",$productInfo[0]);
}else{
    $sql = "SELECT `name` FROM `{$CONFIG_TABLES['product_types']}`;";
    $ptypes=$db->each($sql);
    if(isset($_GET['type'])){
        $searchType = htmlspecialchars(trim($_GET['type']));
        $sql = "SELECT p.*, ptype.name AS `type`, 
        ptype.description AS `type_desc`
        FROM `{$CONFIG_TABLES['products']}` AS p 
        JOIN `{$CONFIG_TABLES['product_types']}` AS ptype 
        ON p.type_id = ptype.id 
        WHERE p.type_id = ? AND p.status IN (1,2) ORDER BY ptype.id ASC;";
        $productsInfo = $db->prepare($sql,[$searchType]);
        if($productsInfo[0]['name']){
            $smarty->assign("type",$productsInfo[0]['type']);
            $smarty->assign("typeDesc",$productsInfo[0]['type_desc']);
        }
    }
    // 印出商品(沒有指定)
    else{
        $sql="SELECT p.*, ptype.name AS `type` 
        FROM `{$CONFIG_TABLES['products']}` AS p 
        JOIN `{$CONFIG_TABLES['product_types']}` AS ptype 
        ON p.type_id = ptype.id WHERE p.status IN (1,2) ;";
        $productsInfo=$db->each($sql);
    }
    foreach($productsInfo as $i => $value){
        $productsInfo[$i]['image_url']=file_exists("{$productImagePath}".$productsInfo[$i]['id'])?"{$productImagePath}{$productsInfo[$i]['id']}?t=".crc32(time()):"assets/images/quaso.png";
    }
    $smarty->assign("products",$productsInfo);
    $smarty->assign("ptypes",$ptypes);
}
?>
