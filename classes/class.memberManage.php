<?php
// include "./class.connectDatabase.php";
// preg_match('/^\d{18,20}$/', $mid)
/****************************************************************************************
 * 
 * 成員系統
 * @param obj $db 資料庫
 * 
 ****************************************************************************************/
class memberManage
{
    private $conn;
    private $member = "m_members";
    private $members_profile = "m_members_profile";
    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 確認使用者輸入之帳戶與密碼是否正確 ###
     * @param string $account 會員帳號 
     * @param string $password 會員密碼
     ************************************************/
    public function checkMemberAccountPassword($account, $password): array
    {
        $account = htmlspecialchars(strtolower($account));
        $password = hash('sha256', $password);
        $sql = "SELECT `id`, `account`, `status` FROM `{$this->member}` WHERE `account` = ? AND `password` = ? LIMIT 1;";
        $result = $this->conn->prepare($sql, [$account, $password]);
        if (empty($result)) return [];
        return $result[0];
    }
    /************************************************
     * ### 取得會員資訊 ###
     * @param int|string $value 會員的編號 或 會員的帳號
     ************************************************/
    public function getMemberInformation($value): array
    {
        $field = 'id';
        if (is_string($value)) {
            $field = 'account';
            $value = htmlspecialchars(strtolower($value));
        }
        $sql = "SELECT * FROM `{$this->member}` AS `member`
        JOIN `{$this->members_profile}` AS `mProfile` ON `mProfile`.`mid` = `member`.`id`
        WHERE `{$field}` = ? LIMIT 1;";
        $result = $this->conn->prepare($sql, [$value]);
        if (empty($result)) return [];
        return $result[0];
    }
    /************************************************
     * ### 新增會員資訊 ###
     * @param int $id 會員的編號
     * @param array $mInformation 會員資訊  
     * ```
     * ["account" => [  
     * "id"=> Int  
     * "account"=> String  
     * "nickname"=> String  
     * "password"=> String  
     * "last_ip_address"=> String  
     * ],  
     * "profile" => [  
     * "introduction"=> String  
     * "theme"=> String  
     * "avatar"=> String  
     * "background"=> String  
     * ]]  
     * ```
     ************************************************/
    public function addMember($mid, $mAccount = []): bool
    {
        $result = true;
        $params = [
            $mid,
            htmlspecialchars(strtolower(trim($mAccount['account']))),
            hash('sha256', $mAccount['password']),
            htmlspecialchars($mAccount['nickname']),
            $mAccount['last_ip_address'],
        ];
        // --------------------------------------------------------------------------------
        // 建立會員帳戶
        $sql = "INSERT INTO `{$this->member}` (`id`, `account`, `password`, `nickname`, `last_ip_address`) VALUES (?, ?, ?, ?, ?);";
        $result &= empty($this->conn->prepare($sql, $params));
        // 建立用戶個人化
        $sql = "INSERT INTO `{$this->members_profile}` (`mid`) VALUES (?);";
        $result &= empty($this->conn->prepare($sql, [$mid]));
        return $result;
    }
    /************************************************
     * ### 更新會員資訊 ###
     * @param int $id 會員的編號
     * @param array $mInformation 會員資訊
     * @param bool $autoCreateAccount 是否自動建立帳戶  
     * ```
     * ["account" => [  
     * "id"=> Int  
     * "account"=> String  
     * "nickname"=> String  
     * "password"=> String  
     * "last_ip_address"=> String  
     * "status"=> 0停用 1啟用 2系統用戶  
     * ],  
     * "profile" => [  
     * "introduction"=> String  
     * "theme"=> String  
     * "avatar"=> String  
     * "background"=> String  
     * ]]  
     * ```
     ************************************************/
    public function updateMemberInformation($mid, $mInformation = ["account" => [], "profile" => []], $autoCreateAccount = false): bool
    {
        $result = true;
        $mAccount = isset($mInformation["account"]) ? $mInformation["account"] : [];
        $mProfile = isset($mInformation["profile"]) ? $mInformation["profile"] : [];
        // 檢查會員原始資訊
        $mOriginalInformation = $this->getMemberInformation($mid);
        // 會員不存在，建立相關頁面
        if (empty($mOriginalInformation)) {
            if ($autoCreateAccount) $result &= $this->addMember($mid, $mAccount);
            else return false;
        }
        // 會員存在，更新會員資訊
        else {
            // 更新會員帳戶資訊
            if (!empty($mAccount)) {
                // 如果沒填的欄位將會以原始資訊填入
                $params = [
                    empty($mAccount['account']) ? $mOriginalInformation['account'] : htmlspecialchars(strtolower(trim($mAccount['account']))),
                    empty($mAccount['password']) ? $mOriginalInformation['password'] : hash('sha256', $mAccount['password']),
                    empty($mAccount['nickname']) ?  $mOriginalInformation['nickname'] : htmlspecialchars($mAccount['nickname']),
                    empty($mAccount['last_ip_address']) ? $mOriginalInformation['last_ip_address'] : $mAccount['last_ip_address'],
                    empty($mAccount['status']) ? $mOriginalInformation['status'] : $mAccount['status'],
                    $mid
                ];
                $sql = "UPDATE `{$this->member}` SET `account` = ?, `password` = ?, `nickname` = ? , `last_ip_address` = ? `status` = ? WHERE `id` = ?";
                $result &= empty($this->conn->prepare($sql, $params));
            }
            // 更新會員個人化
            if (!empty($mProfile)) {
                // 如果沒填的欄位將會以原始資訊填入
                $params = [
                    htmlspecialchars(empty($mProfile['introduction']) ? $mOriginalInformation['introduction'] : $mProfile['introduction']),
                    empty($mProfile['avatar']) ? $mOriginalInformation['avatar'] : $mProfile['avatar'],
                    empty($mProfile['theme']) ? $mOriginalInformation['theme'] : $mProfile['theme'],
                    empty($mProfile['background']) ? $mOriginalInformation['background']  : $mProfile['background'],
                    $mid
                ];
                $sql = "UPDATE `{$this->members_profile}` SET `introduction` = ?, `theme` = ?, `avatar` = ? , `background`= ? WHERE `id` = ?";
                $result &= empty($this->conn->prepare($sql, $params));
            }
        }
        return $result;
    }
}