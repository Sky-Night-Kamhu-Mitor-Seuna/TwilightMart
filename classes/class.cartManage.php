<?php
// include "./class.connectDatabase.php";
/****************************************************************************************
 * 
 * 頁面元件
 * @param obj $db 資料庫
 * 
 ****************************************************************************************/
class cartManage
{
    private $conn;
    private $cart = 'i_cart';
    private $products = "i_products";
    // private $products = "i_products";

    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 確認商品數量 ###
     * 注意如果客制化選項(規格、顏色)不相同將視為不存在購物車  
     * @param int $productId 商品編號
     * @param int $wid 網站編號
     * @param int $mid 會員編號
     * @param int $specification 規格
     * @param int $color 顏色
     ************************************************/
    private function checkProductQuantity($productId, $wid, $mid, $specification, $color): array
    {
        $sql = "SELECT `id`, `quantity` FROM {$this->cart} WHERE `product_id` = ? AND `wid` = ? AND `mid` = ? AND `specification` = ? AND `color` = ? AND `quantity` <> 0;";
        $result = $this->conn->prepare($sql, [$productId, $wid, $mid, $specification, $color]);
        if (empty($result)) return [];
        return $result[0];
    }
    /************************************************
     * ### 新增商品至購物車 ###
     * @param int $id 購物車編號
     * @param int $productId 商品編號
     * @param int $wid 網站編號
     * @param int $mid 會員編號
     * @param int $quantity 數量
     * @param int $specification 規格
     * @param int $color 顏色
     ************************************************/
    public function addCart($id, $productId, $wid, $mid, $quantity, $specification = "[]", $color = "[]"): int
    {
        $inCartInformations = $this->checkProductQuantity($productId, $wid, $mid, $specification, $color);
        if (empty($inCartInformations)) {
            $sql = "INSERT INTO {$this->cart} (`id`, `product_id`, `wid`, `mid`, `specification`, `color`, `quantity`) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $result = $this->conn->prepare($sql, [$id, $productId, $wid, $mid, $specification, $color, $quantity]);
        } else {
            $id = $inCartInformations['id'];
            $sql = "UPDATE {$this->cart} SET `quantity` = ? WHERE `id` = ?;";
            $result = $this->conn->prepare($sql, [($quantity + $inCartInformations['quantity']), $inCartInformations['id']]);
        }
        return empty($result) ? $id : 0;
    }
    /************************************************
     * ### 新增商品至購物車 ###
     * @param int $id 購物車編號
     * @param int $productId 商品編號
     * @param int $wid 網站編號
     * @param int $mid 會員編號
     * @param int $specification 規格
     * @param int $color 顏色
     ************************************************/
    public function delCart($id): bool
    {
        $sql = "SELECT COUNT(*) FROM {$this->cart} WHERE `id` = ?";
        if (empty($this->conn->prepare($sql, [$id]))) return false;
        
        $sql = "UPDATE {$this->cart} SET `quantity` = 0 WHERE `id` = ?;";
        $result = $this->conn->prepare($sql, [$id]);
        return empty($result);
    }
    /************************************************
     * ### 取得購物車內容 ###
     * Note: 優化查詢
     * @param int $wid 網站編號
     * @param int $mid 會員編號
     ************************************************/
    public function getCart($wid, $mid, $cartId = null): array
    {
        $sql = "SELECT `cart`.`id`, `cart`.`product_id`, `products`.`name`, `products`.`description`, `products`.`types` , 
        `cart`.`specification`, `cart`.`color`, 
        `cart`.`quantity`, `products`.`price`
        FROM {$this->cart} AS `cart`
        JOIN {$this->products} AS `products` ON `cart`.`product_id` = `products`.`id`
        WHERE `cart`.`mid` = ? AND `cart`.`wid` = ? AND `cart`.`quantity` <> 0 
        AND `products`.`status` <> 0 ".(is_null($cartId) ? "" : "AND `cart`.`id` = ? ").";";
        $arr = is_null($cartId) ? [$mid, $wid] : [$mid, $wid, $cartId];
        $result = $this->conn->prepare($sql, $arr);
        return empty($result) ? [] : $result;
    }
}
