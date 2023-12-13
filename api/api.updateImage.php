<?php

/****************************************************************************************
 * 
 * 更新圖片
 * 
 ****************************************************************************************/
require_once "../sys.global.php";
/****************************************************************************************/
if (isset($_SESSION['mid']) &&  isset($_POST['action']) && isset($_POST['path'])) {
    $command = "";
    header("Content-type: application/json");
    switch ($_POST['action']) {
        case "USER_CHANGE_AVATAR":
            $sql="UPDATE `".CONFIG_TABLES['members_profile']."` SET `avatar` = ? WHERE `mid` = ?;";
            $db->prepare($sql, ["{$_POST['path']}?v=".$sf->getId(), $_SESSION['mid']]);
            $command = "USER_CHANGE_AVATAR";
            break;
        case "WEBSITE_CHANGE_ICON":
            if(!$permissions->checkMemberPermissions($_SESSION['mid'], WEBSITE_ID, 4)){
                echo json_encode([]);
                exit;
            }
            $sql="UPDATE `".CONFIG_TABLES['website']."` SET `icon` = ? WHERE `id` = ?;";
            $db->prepare($sql, ["{$_POST['path']}?v=".$sf->getId(), WEBSITE_ID]);
            $command = "WEBSITE_CHANGE_ICON";
            break;
        case "PRODUCT_CHANGE_IMAGE":
            if(!isset($_POST['id']) && !$permissions->checkMemberPermissions($_SESSION['mid'], WEBSITE_ID, 32)){
                echo json_encode([]);
                exit;
            }
            $sql="UPDATE `".CONFIG_TABLES['products']."` SET `images` = ? WHERE `id` = ? AND `wid` = ?;";
            $db->prepare($sql, ['["'.$_POST['path'].'?v='.$sf->getId().'"]',$_POST['id'], WEBSITE_ID]);
            $command = "PRODUCT_CHANGE_IMAGE";
            break;
    }
    $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['mid'], $USER_IP_ADDRESS, $command, 1);
    echo json_encode([]);
} else {
    // print_r($_POST);
    include_once "../404/index.php";
}
// $_FILES['IMAGE_UPLOAD']['error'] === UPLOAD_ERR_OK

