<?php

/****************************************************************************************
 * 
 * 變更商品狀態
 * 
 ****************************************************************************************/
require_once "../sys.global.php";
/****************************************************************************************/
if (isset($_SESSION['mid']) && isset($_POST['action'])) {
    header("Content-type: application/json");
    $command = "";
    if (!$permissions->checkMemberPermissions($_SESSION['mid'], WEBSITE_ID, 32)) {
        echo json_encode([]);
        exit;
    }
    switch (($_POST['action'])) {
        case "change_status":
            if (!isset($_POST['product_id'])) exit;
            $productManage->changeProductStatus($_POST['product_id'], WEBSITE_ID, $_POST['product_status']);
            $command = "CHANGE_PRODUCT";
            break;
        case "add_product":
            if (isset($_POST['product_name']) && isset($_POST['product_price']) && isset($_POST['product_quantity'])) {
                if (preg_match('/^[.0-9]+$/', $_POST['product_price']) || preg_match('/^[0-9]+$/', $_POST['product_quantity'])){
                    $productName = ($_POST['product_name'] != '') ? htmlspecialchars($_POST['product_name']) : "未命名商品";
                    $productQuantity = intval($_POST['product_quantity']);
                    $productPrice = floatval($_POST['product_price']);
                    $result = $productManage->addProduct($sf->getId(), WEBSITE_ID, $productName, $productPrice, $productQuantity);
                    // $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['mid'], $USER_IP_ADDRESS, "ADD_PRODUCT", !empty($result));
                    $command = "ADD_PRODUCT";
                }
            }
    }
    echo json_encode([]);
    $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['mid'], $USER_IP_ADDRESS, $command, 1);
} else {
    include_once "../404/index.php";
}
// $_FILES['IMAGE_UPLOAD']['error'] === UPLOAD_ERR_OK
