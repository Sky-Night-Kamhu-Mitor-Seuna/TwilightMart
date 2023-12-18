<?php
// include "./class.connectDatabase.php";

use function PHPSTORM_META\type;

/****************************************************************************************
 * 
 * 商品系統
 * @param obj $db 資料庫
 * 
 ****************************************************************************************/
class productManage
{
    private $conn;
    private $products = "i_products";
    private $product_views = "log_product_views";
    private $orders = "p_orders";
    private $order_item = "p_order_items";
    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 取得商品資訊 ###
     * @param int $wid 網站編號
     * @param int $product_id 商品編號
     ************************************************/
    public function getProductInformation($wid, $product_id = null, $autoJsonDecode = true): array
    {
        $field = '';
        $params[] = $wid;
        if (!is_null($product_id)) {
            $field = "AND `id` = ?";
            $params[] = $product_id;
        }
        $sql = "SELECT * FROM `{$this->products}` WHERE `wid` = ? {$field} ORDER BY `status` DESC;";
        $result = $this->conn->prepare($sql, $params);
        if (empty($result)) return [];
        foreach ($result as $key => $res) {
            if ($autoJsonDecode) {
                $result[$key]['tags'] = json_decode($res['tags'], true);
                $result[$key]['images'] = json_decode($res['images'], true);
                $result[$key]['specification'] = json_decode($res['specification'], true);
                $result[$key]['color'] = json_decode($res['color'], true);
            }
            $result[$key]['cover_photo'] = json_decode($res['images'], true)[0];
        }
        return is_null($product_id) ? $result : $result[0];
    }
    /************************************************
     * ### 新增商品 ###
     * 注意建立後的商品默認都為未啟用，這是設計不是BUG  
     * 
     * @param int $wid 網站id
     * @param int $productId 商品id
     * @param string $name 商品名稱  
     * ```
     * [
     * "name" => String,
     * "description" => String,
     * "types" => Json,
     * "tags" => Json),
     * "price" => Int,
     * "specification" => Json,
     * "color" => Json,
     * "quantity" => Float,
     * ]
     * ```
     ************************************************/
    public function addProduct($wid, $product_id, $pInformation = []): bool
    {
        $checkExist = $this->getProductInformation($wid, $product_id);
        if (!empty($checkExist)) return false;
        $result = true;
        if (!preg_match('/^[.0-9]+$/', $pInformation['price']) || !is_int($pInformation['quantity']) || $pInformation['price'] < 0 || $pInformation['quantity'] < -2) return false;
        $params = [
            $product_id,
            $wid,
            empty($pInformation['name']) ? "未命名商品" : htmlspecialchars($pInformation['name']),
            empty($pInformation['description']) ? "無介紹" : htmlspecialchars($pInformation['description']),
            empty($pInformation['types']) ? "[]" : json_encode(htmlspecialchars($pInformation['types'])),
            empty($pInformation['tags']) ? "[]" : json_encode(htmlspecialchars($pInformation['tags'])),
            empty($pInformation['price']) ? 1 : $pInformation['price'],
            empty($pInformation['specification']) ? "[]" : json_encode(htmlspecialchars($pInformation['specification'])),
            empty($pInformation['color']) ? "[]" : json_encode(htmlspecialchars($pInformation['color'])),
            empty($pInformation['quantity']) ? -1 : $pInformation['quantity'],
        ];
        // --------------------------------------------------------------------------------
        $sql = "INSERT INTO {$this->products} (`id`, `wid`, `name`, `description`, `types`, `tags`, `price`, `specification`, `color`, `quantity`) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $result &= empty($this->conn->prepare($sql, $params));
        return $result;
    }
    /************************************************
     * ### 編輯商品 ###
     * 注意建立後的商品默認都為未啟用，這是設計不是BUG  
     * 
     * @param int $wid 網站id
     * @param int $productId 商品id
     * ```
     * [
     * "name" => String,
     * "description" => String,
     * "types" => Json,
     * "tags" => Json),
     * "price" => Int,
     * "specification" => Json,
     * "color" => Json,
     * "quantity" => Float,
     * "status" => Int
     * ]
     * ```
     ************************************************/
    public function updateProductInformation($wid, $product_id, $pInformation = []): bool
    {
        $result = true;
        $pOriginalInformation = $this->getProductInformation($wid, $product_id, false);
        if (!empty($checkExist)) return false;
        // if (!preg_match('/^[.0-9]+$/', $pInformation['price']) || !is_int($pInformation['quantity']) || $pInformation['price'] < 0 || $pInformation['quantity'] < -2) return false;
        $params = [
            empty($pInformation['name']) ? $pOriginalInformation['name'] : htmlspecialchars($pInformation['name']),
            empty($pInformation['description']) ? $pOriginalInformation['description'] : htmlspecialchars($pInformation['description']),
            empty($pInformation['types']) ? $pOriginalInformation['types'] : json_encode(htmlspecialchars($pInformation['types'])),
            empty($pInformation['tags']) ? $pOriginalInformation['tags'] : json_encode(htmlspecialchars($pInformation['tags'])),
            empty($pInformation['specification']) ? $pOriginalInformation['specification'] : json_encode(htmlspecialchars($pInformation['specification'])),
            empty($pInformation['color']) ? $pOriginalInformation['color'] : json_encode(htmlspecialchars($pInformation['color'])),
            empty($pInformation['price']) ? $pOriginalInformation['price'] : $pInformation['price'],
            empty($pInformation['quantity']) ? $pOriginalInformation['quantity'] : $pInformation['quantity'],
            empty($pInformation['status']) ? $pOriginalInformation['status'] : $pInformation['status'],
            $product_id,
            $wid
        ];
        // --------------------------------------------------------------------------------
        $sql = "UPDATE `{$this->products}` SET `name` = ?, `description` = ?, `types` = ?, `tags` = ?, 
        `specification` = ?, `color` = ?, `price` = ?, `quantity` = ?, `status` = `status` ^ ? WHERE `id` = ? AND `wid` = ?;";
        $result &= empty($this->conn->prepare($sql, $params));
        return $result;
    }
    /************************************************
     * ### 查找商品 ###
     * @param obj $db 資料庫
     ************************************************/
    public function searchProduct($wid, $name):array
    {
        $sql = "SELECT `id` FROM {$this->products} WHERE `wid` = ? AND `name` LIKE ?;";
        $result=$this->conn->prepare($sql, [$wid, "%{$name}%"]);
        return empty($result) ? [] : $result;
    }
}
