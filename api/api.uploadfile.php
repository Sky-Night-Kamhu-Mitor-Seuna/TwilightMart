<?php

/****************************************************************************************
 * 
 * 上傳檔案系統
 * 
 ****************************************************************************************/
require_once "../sys.global.php";
/****************************************************************************************/
if (isset($_SESSION['mid']) &&  isset($_POST['action']) && isset($_FILES['uploadfile'])) {
    header("Content-type: application/json");
    switch ($_POST['action']) {
        case "USER_CHANGE_AVATAR":
            $file->setPath("avatar");
            $filePath = $file->upload($_FILES['uploadfile'], $_SESSION['mid']);
            break;
        case "WEBSITE_CHANGE_ICON":
            if(!$permissions->checkMemberPermissions($_SESSION['mid'], WEBSITE_ID, 4)){
                echo json_encode([]);
                exit;
            }
            $file->setPath("website");
            $filePath = $file->upload($_FILES['uploadfile'], WEBSITE_ID);
            break;
        case "PRODUCT_CHANGE_IMAGE":
            if(!isset($_POST['id']) || !$permissions->checkMemberPermissions($_SESSION['mid'], WEBSITE_ID, 32)){
                echo json_encode([]);
                exit;
            }
            $file->setPath("products"); 
            $filePath = $file->upload($_FILES['uploadfile'], $_POST['id']);
            break;
    }
    echo json_encode([$filePath]);
    $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['mid'], $USER_IP_ADDRESS, "UPLOAD_FILE", 1);
} else {
    // print_r($_POST);
    include_once "../404/index.php";
}
// $_FILES['IMAGE_UPLOAD']['error'] === UPLOAD_ERR_OK


    
