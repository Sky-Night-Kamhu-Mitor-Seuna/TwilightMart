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
     * @param int $wid 網站id
     ************************************************/
    public function getProductInformation($wid, $product_id=0): array
    {
        $sql = "SELECT * FROM {$this->products} WHERE `wid` = ? ".($product_id != 0 ? "AND `id` = ? " : "")." ORDER BY `status` DESC;";
        $params = ($product_id != 0) ? [$wid, $product_id] : [$wid] ;
        $result = $this->conn->prepare($sql, $params);
        if (empty($result)) return [];
        foreach ($result as $key => $res){
            $result[$key]['tags'] = json_decode($res['tags'],true);
            $result[$key]['images'] = json_decode($res['images'],true);
            $result[$key]['cover_photo'] = $result[$key]['images'][0];
        }
        return $result;
    }
    /************************************************
     * ### 確認商品狀態 ###
     * @param int $productId 商品id
     ************************************************/
    public function checkProductStatus($productId): int
    {
        $sql = "SELECT `status` FROM {$this->products} WHERE `id` = ? ;";
        $result = $this->conn->prepare($sql, [$productId]);
        return empty($result) ? $result[0]['status'] : -1;
    }
    /************************************************
     * ### 過濾商品參數 ###
     * @param json $types 商品類別
     * @param json $tags 商品標籤(注意這是由系統去判斷的而不是商家)
     * @param json $specification 規格(看商品而改變其資訊)
     * @param json $color 顏色(看商品而改變其資訊)
     ************************************************/
    public function formatProductParams($types = [], $tags = [], $specification = [], $color = []): array
    {
        $processedParams = [
            'types' => array_map('htmlspecialchars', $types),
            'tags' => array_map('htmlspecialchars', $tags),
            'spec' => array_map('htmlspecialchars', $specification),
            'color' => array_map('htmlspecialchars', $color)
        ];
        foreach ($processedParams as $key => $value)  $processedParams[$key] = json_encode($value);
        return $processedParams;
    }
    /************************************************
     * ### 新增商品 ###
     * 注意建立後的商品默認都為未啟用，這是設計不是BUG  
     * 
     * @param int $productId 商品id
     * @param int $wid 網站id
     * @param string $name 商品名稱
     * @param float $price 售價
     * @param int $quantity 殘存數量
     * @param string $description 商品介紹
     * @param json $types 商品類別
     * @param json $tags 商品標籤(注意這是由系統去判斷的而不是商家)
     * @param json $specification 規格(看商品而改變其資訊)
     * @param json $color 顏色(看商品而改變其資訊)
     ************************************************/
    public function addProduct($id, $wid, $name, $price, $quantity = -1, $description = "", $types = [], $tags = [], $specification = [], $color = []): bool
    {
        if (!preg_match('/^[.0-9]+$/', $price) || !is_int($quantity) || $price < 0 || $quantity < -2) return false;
        $productParams = $this->formatProductParams($types, $tags, $specification, $color);
        $sql = "INSERT INTO {$this->products} (`id`, `wid`, `name`, `description`, `types`, `tags`, `specification`, `color`, `price`, `quantity`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";
        $result = $this->conn->prepare($sql, [$id, $wid, htmlspecialchars($name), htmlspecialchars($description), $productParams['types'], $productParams['tags'], $productParams['spec'], $productParams['color'], $price, $quantity]);
        return empty($result);
    }
    /************************************************
     * ### 編輯商品 ###
     * 注意建立後的商品默認都為未啟用，這是設計不是BUG  
     * 
     * @param int $productId 商品id
     * @param int $wid 網站id
     * @param string $name 商品名稱
     * @param float $price 售價
     * @param int $quantity 殘存數量
     * @param string $description 商品介紹
     * @param json $types 商品類別
     * @param json $tags 商品標籤(注意這是由系統去判斷的而不是商家)
     * @param json $specification 規格(看商品而改變其資訊)
     * @param json $color 顏色(看商品而改變其資訊)
     ************************************************/
    public function editProduct($id, $wid, $name, $price, $quantity = -1, $description = "", $types = [], $tags = [], $specification = [], $color = [], $status): bool
    {
        $productParams = $this->formatProductParams($types, $tags, $specification, $color);
        $sql = "UPDATE {$this->products} SET `name` = ?,  `description` = ?, `types` = ?, `tags` = ?, `specification` = ?, `color` = ? `price` = ?, `quantity` = ? WHERE `wid` = ? AND `id` = ?;";
        $result = $this->conn->prepare($sql, [$id, $wid, htmlspecialchars($name), htmlspecialchars($description), $productParams['types'], $productParams['tags'], $productParams['spec'], $productParams['color'], $price, $quantity]);
        return empty($result);
    }
    /************************************************
     * ### 變更商品狀態商品 ###  
     * 
     * @param int $productId 商品id
     * @param int $wid 網站id
     * @param int $status 狀態
     ************************************************/
    public function changeProductStatus($id, $wid, $status ): bool
    {
        $sql = "UPDATE {$this->products} SET `status` = ? WHERE `wid` = ? AND `id` = ?;";
        $result = $this->conn->prepare($sql, [$status, $wid, $id]);
        return empty($result);
    }
}
