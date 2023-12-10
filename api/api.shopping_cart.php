<?php

/****************************************************************************************
 * 
 * 取得資料庫購物車資訊
 * 
 ****************************************************************************************/
require_once "../sys.global.php";
$cart = new cartManage($db);
/****************************************************************************************/
if (isset($_REQUEST ['action']) && isset($_SESSION['mid'])) 
{
    // $systemAction = "CHANGE_CART";
    $memberId = $_SESSION['mid'];
    $action = empty($_REQUEST ['action']) ? null : $_REQUEST ['action'];
    $cartId = isset($_REQUEST ['cart_id']) ? $_REQUEST ['cart_id'] : null;

    $productId = isset($_REQUEST ['product_id']) ? intval($_REQUEST ['product_id']) : null;
    $productQuantity = isset($_REQUEST ['product_quantity']) ? intval($_REQUEST ['product_quantity']) : null;
    $productSpecification = isset($_REQUEST ['product_specification']) ? $_REQUEST ['product_specification'] : "[]";
    $productColor = isset($_REQUEST ['product_color']) ? $_REQUEST ['product_color'] : "[]";
    header("Content-type: application/json");
    switch ($action) {
        case "UPDATE":
            if (!is_null($productId) && !is_null($productQuantity)) $cartId = $cart->addCart($sf->getId(), $productId, WEBSITE_ID, $memberId, $productQuantity, $productSpecification, $productColor);
        case "VIEW":
            // 由$cartId決定是否輸出單個還是全部的清單
            if ($cartId) $result = $cart->getCart(WEBSITE_ID, $_SESSION['mid'], $cartId);
            else $result = $cart->getCart(WEBSITE_ID, $_SESSION['mid']);
            $result = $cart->getCart(WEBSITE_ID, $_SESSION['mid']);
            // 將CartId設為鍵值
            foreach ($result as $key => $value) {
                $res[$value['id']] = $value;
                unset($res[$value['id']]['id']);
            }
            echo json_encode((empty($result) ? [] : $res));
            exit;
        case "DEL":
            if (!is_null($cartId)) $result = $cart->delCart($cartId);
            echo json_encode("OK");
            exit;
        default:
    }
    // $log->addSystemLog($sf->getId(), WEBSITE_ID, (isset($_SESSION['mid']) ? $_SESSION['mid'] : $_SESSION['sessionId']), $USER_IP_ADDRESS, $systemAction, 1);
} else 
{
    // $log->addSystemLog($sf->getId(), WEBSITE_ID, (isset($_SESSION['mid']) ? $_SESSION['mid'] : $_SESSION['sessionId']), $USER_IP_ADDRESS, "VIEW_CART", 0);
    include_once "../404/index.php";
}
