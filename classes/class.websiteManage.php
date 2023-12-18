<?php

/****************************************************************************************
 * 
 * 網站系統
 * @param obj $db 資料庫
 * 
 ****************************************************************************************/
class websiteManage
{
    private $conn;
    private $website = "s_website";
    private $newebpay = "s_newebpay";
    private $pages = "w_pages";
    private $page_component = "w_page_component";
    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 取得網站資訊 ###
     * @param int|string $value 網站的編號 或 網域名稱
     ************************************************/
    public function getWebsiteInformation($value): array
    {
        $field = 'id';
        if (is_string($value)) $field = 'domain';
        $sql = "SELECT * FROM `{$this->website}` AS `website`
        JOIN `{$this->newebpay}` AS `newebpay` ON `newebpay`.`wid` = `website`.`id`
        WHERE `{$field}` = ? LIMIT 1;";

        $result = $this->conn->prepare($sql, [$value]);
        if (empty($result)) return [];
        return $result[0];
    }
    /************************************************
     * ### 初始化網站 ###
     * @param int $wid 網站的編號
     * @param string $domainName 網域名稱
     ************************************************/
    public function initWebsite($wid, $domainName): bool
    {
        $result = true;
        $domainName = htmlspecialchars($domainName);
        $res = $this->getWebsiteInformation($domainName);
        // 如果沒有建立過的網域才能建立新網站
        if (empty($res)) {
            $wParams = [
                $wid,
                $domainName,
                'Demo',
                'DemoWebsite',
                'Taiwan',
                '/assets/images/logo.png',
            ];
            $nParams = [
                $wid,
                '0',
                '0',
                '0',
                '0',
                "https://{$domainName}/projects/steam",
                "https://{$domainName}/projects/steam",
                "https://{$domainName}/projects/steam/api/done.php"
            ];
            // --------------------------------------------------------------------------------
            $sql = "INSERT INTO `{$this->website}` (`id`, `domain`, `name`, `displayname`, `distribution`, `icon`) 
            VALUES (?, ?, ?, ?, ?, ?);";
            $result &= empty($this->conn->prepare($sql, $wParams));
            // --------------------------------------------------------------------------------
            $sql = "INSERT INTO `{$this->newebpay}` VALUES(?, ?, ?, ?, ?, ?, ?, ?);";
            $result &= empty($this->conn->prepare($sql, $nParams));
            return $result;
        } else return false;
    }
    /************************************************
     * ### 更新網站資訊 ###
     * @param int $wid 網站編號
     * @param array $wInformation 網站資訊    
     * @param bool $autoCreateWebsite 如果不存在是否建立  
     * ```
     * ["website" => [  
     * "domain"=> String  
     * "name"=> String  
     * "distribution"=> String  
     * "icon"=> String  
     * "background"=> String  
     * "stylesheet"=> String  
     * "theme"=> String
     * ],  
     * ["newebpay" =>[  
     * "store_prefix"=> String  
     * "store_id"=> String  
     * "store_hash_key"=> String  
     * "store_hash_iv"=> String  
     * "store_return_url"=> String  
     * "store_client_back_url"=> String  
     * "store_notify_url"=> String  
     * ]]
     * ```
     ************************************************/
    public function updateWebsiteInformation($wid, $wInformation = ["website" => [], "newebpay" => []]): bool
    {
        $result = true;
        $wOriginalInformation = $this->getWebsiteInformation($wid);
        if (!empty($wOriginalInformation)) return false;
        // if (!preg_match('/^[a-zA-Z0-9_\-@$.\s]+$/', $wInformation)) return false;
        $website = isset($wInformation["website"]) ? $wInformation["website"] : [];
        $newebpay = isset($wInformation["newebpay"]) ? $wInformation["newebpay"] : [];
        // 網站簡碼只能使用英文數字及_、-
        // if (!preg_match('/^[a-zA-Z0-9_\-]+$/', $website['name'])) return false;
        if (!empty($website)) {
            $params = [
                empty($website['domain']) ? $wOriginalInformation['domain'] : $website['domain'],
                empty($website['name']) ? $wOriginalInformation['name'] : $website['name'],
                empty($website['displayname']) ? $wOriginalInformation['displayname'] : $website['displayname'],
                empty($website['distribution']) ? $wOriginalInformation['distribution'] : $website['distribution'],
                empty($website['icon']) ? $wOriginalInformation['icon'] : $website['icon'],
                empty($website['background']) ? $wOriginalInformation['background'] : $website['background'],
                empty($website['stylesheet']) ? $wOriginalInformation['stylesheet'] : $website['stylesheet'],
                empty($website['theme']) ? $wOriginalInformation['theme'] : $website['theme'],
                $wid
            ];
            $sql = "UPDATE `{$this->website}` SET `domain` = ?, `name` =?, 
                `displayname` = ?, `distribution` = ?, `icon` = ?, `background` = ?, `stylesheet` =?, `theme` =? 
                WHERE `id` = ? ;";
            $result &= empty($this->conn->prepare($sql, $params));
        }
        if (!empty($newebpay)) {
            $params = [
                empty($newebpay['store_prefix']) ? $wOriginalInformation['store_prefix'] : $newebpay['store_prefix'],
                empty($newebpay['store_id']) ? $wOriginalInformation['store_id'] : $newebpay['store_id'],
                empty($newebpay['store_hash_key']) ? $wOriginalInformation['store_hash_key'] : $newebpay['store_hash_key'],
                empty($newebpay['store_hash_iv']) ? $wOriginalInformation['store_hash_iv'] : $newebpay['store_hash_iv'],
                empty($newebpay['store_return_url']) ? $wOriginalInformation['store_return_url'] : $newebpay['store_return_url'],
                empty($newebpay['store_client_back_url']) ? $wOriginalInformation['store_client_back_url'] : $newebpay['store_client_back_url'],
                empty($newebpay['store_notify_url']) ? $wOriginalInformation['store_notify_url'] : $newebpay['store_notify_url'],
                $wid
            ];
            $sql = "UPDATE `{$this->newebpay}` SET `store_prefix` = ?, `store_id` =?, `store_hash_key` = ?, `store_hash_iv` = ?, 
                `store_return_url` = ?, `store_client_back_url` = ?, `store_notify_url` =? WHERE `wid` = ? ;";
            $result &= empty($this->conn->prepare($sql, $params));
        }
        return $result;
    }
}
