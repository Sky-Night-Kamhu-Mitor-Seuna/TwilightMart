<?php

/****************************************************************************************
 * 
 * 取得資料庫購物車資訊
 * 
 ****************************************************************************************/
require_once "../sys.global.php";
$cart = new cartManage($db);
/****************************************************************************************/
if (isset($_POST ['action']) && isset($_SESSION['mid'])) 
{
    // $systemAction = "CHANGE_CART";
    $memberId = $_SESSION['mid'];
    $action = empty($_POST ['action']) ? null : $_POST ['action'];
    $cartId = isset($_POST ['cart_id']) ? $_POST ['cart_id'] : null;

    $productId = isset($_POST ['product_id']) ? intval($_POST ['product_id']) : null;
    $productQuantity = isset($_POST ['product_quantity']) ? intval($_POST ['product_quantity']) : null;
    $productSpecification = isset($_POST ['product_specification']) ? $_POST ['product_specification'] : "[]";
    $productColor = isset($_POST ['product_color']) ? $_POST ['product_color'] : "[]";
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
