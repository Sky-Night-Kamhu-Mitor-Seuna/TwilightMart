<?php
// include "./class.connectDatabase.php";
/****************************************************************************************
 * 
 * 商品系統
 * @param obj $db 資料庫
 * 
 ****************************************************************************************/
class productsRecommend
{
    private $conn;
    private $products = "i_products";
    private $product_views = "log_product_views";
    private $search = "log_search";
    private $orders = "p_orders";
    private $order_item = "p_order_items";
    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 取得推播商品資訊 ###
     * @param int $wid 網站id
     * @param int $mid 用戶id 
     ************************************************/
    public function getRecommendProductInformation($wid, $mid=0): array
    {
        $sql = "SELECT FROM {$this->product_views} WHERE `wid` = ?AND `operator` = ?";
        "SELECT product_views.*, product.*, `order`.*, order_item.* 
        FROM `log_product_views` AS `product_views` 
        JOIN `i_products` AS `product` ON `product`.id = `product_views`.product_id
        JOIN `log_page_views` AS `page_view` ON `page_view`.id = `product_views`.vid
        JOIN `m_members` AS `members` ON `members`.id = `page_view`.operator
        JOIN `p_orders` AS `order` ON `order`.`mid` = `members`.id 
        JOIN `p_order_items` AS `order_item` ON order_item.`order_id` = `order`.id;";
        $result = $this->conn->prepare($sql, [$wid, $mid]);
        if (empty($result)) {}
        foreach ($result as $res) {
            $res['tags'] = json_decode($res['tags'], true);
        }
        return $res;
    }
}
