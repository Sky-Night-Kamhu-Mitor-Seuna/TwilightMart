<?php
// include "./class.connectDatabase.php";
// preg_match('/^\d{18,20}$/', $mid)
/****************************************************************************************
 * 
 * 權限系統
 * @param obj $db 資料庫
 * 
 ****************************************************************************************/
class permissions
{
    private $conn;
    private $member_roles = "m_member_roles";
    private $roles = "m_roles";
    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 設置權限表 ###
     * @param string $member_roles
     * @param string $role_permissions
     * @param string $roles
     * @param string $permissions
     ************************************************/
    public function setTables($member_roles, $roles, $permissions): bool
    {
        $this->member_roles = $member_roles;
        // $this->role_permissions = $role_permissions;
        $this->roles = $roles;
        // $this->permissions = $permissions;
        return true;
    }
    /************************************************
     * ### 確認身份組權限 ###
     * @param int $rid 身份組id
     * @param bool $containsParentPermission 是否包含父身分組權限
     ************************************************/
    private function getRolePermissions($rid, $containsParentPermission = true): int
    {
        $sql = "SELECT `permissions`,`parent_id` FROM {$this->roles} WHERE `id` = ? AND `status` <> 0;";
        $result = $this->conn->prepare($sql, [$rid]);
        if (empty($result)) return 0;
        else $permissions = $result[0]['permissions'];

        if ($result[0]['parent_id'] != $rid && !is_null($result[0]['parent_id']) && $containsParentPermission) {
            $permissions |= $this->getRolePermissions($result[0]['parent_id']);
        }
        return $permissions;
    }
    /************************************************
     * ### 新增身份組 ###
     * @param int $wid 網站編號
     * @param int $rid 身分組編號
     ************************************************/
    private function addRoles($wid, $rid): bool
    {
        $sql = "INSERT INTO `{$this->roles}` (`id`, `wid`, `name`, `displayname`, `parent_id`, `permissions`) VALUES (?, ?, ?, ?, ?, ?);";
        $result = empty($this->conn->prepare($sql, [$rid, $wid, "newRole", "未命名身分祖", $wid, 0]));
        return empty($result);
    }
    /************************************************
     * ### 確認使用者身份組 ###
     * @param int $mid 使用者編號
     * @param int $wid 網站編號
     ************************************************/
    public function getMemberRoles($mid, $wid): array
    {
        $sql = "SELECT `role`.`displayname`, `role`.`name`, `role`.`id` 
        FROM `{$this->member_roles}` AS `memberRole` 
        JOIN `{$this->roles}` AS `role` ON `role`.`id` = `memberRole`.`rid`
        WHERE `memberRole`.`mid` = ? AND `memberRole`.`wid` = ? 
        AND `role`.`name` <> 'everyone' AND `role`.`status` <> 0 ;";
        $roles = $this->conn->prepare($sql, [$mid, $wid]);
        return $roles;
    }
    /************************************************
     * ### 確認使用者是否能存取物件 ###
     * @param int $mid 使用者編號
     * @param int $wid 網站編號
     * @param int $permission 權限編號
     ************************************************/
    public function checkMemberPermissions($mid, $wid, $permission): bool
    {
        $result = false;
        $roles = $this->getMemberRoles($mid, $wid);
        foreach ($roles as $role) {
            $permissions = $this->getRolePermissions($role['id'], true);
            // 1 為最高權限
            if (($permission & $permissions) || (0x1 & $permissions)) {
                $result = true;
                break;
            }
        }
        return $result;
    }
    /************************************************
     * ### 修改用戶身份組 ###
     * @param int $mid 使用者編號
     * @param int $rid 身份組編號
     * @param int $wid 網站編號
     ************************************************/
    public function changeMemberRoles($mid, $wid, $rid): bool
    {
        $memberHaveRole = in_array($rid, $this->getMemberRoles($mid, $wid));
        if ($memberHaveRole){
            $sql = "DELETE FROM `{$this->member_roles}` WHERE `mid` = ? AND `rid` = ? AND `wid` = ?;";
        }
        else{
            $sql = "INSERT INTO `{$this->member_roles}` (`mid`, `rid`, `wid`) VALUES (?, ?, ?);";
        }
        $result = $this->conn->prepare($sql, [$mid, $rid, $wid]);
        return empty($result);
    }
    /************************************************
     * ### 取得身分組資訊 ###
     * @param int $wid 網站編號
     * @param int $rid 身份組編號(留空將設置為所有身分組)
     * @param int $containsParentPermission 是否包含父身分組權限(默認false)
     ************************************************/
    public function getRoleInformation($wid, $rid = null, $containsParentPermission = false): array
    {
        $params[] = $wid;
        $roleCondition = "";
        if (!is_null($rid)) {
            $params[] = $rid;
            $roleCondition = "AND `roles`.`id` = ?";
        }
        $sql = "SELECT * FROM `{$this->roles}` AS `roles` WHERE `wid` = ? AND `status` > 0 {$roleCondition} ;";
        $result = $this->conn->prepare($sql, $params);
        if (empty($result)) return [];
        if ($containsParentPermission) {
            foreach ($result as $key => $rInfo) {
                $result[$key]['genealogy'][] = $rInfo['parent_id'];
                $result[$key]['permission'] |= $this->getRolePermissions($rid, $containsParentPermission);
            }
        }
        return $result;
    }
    /************************************************
     * ### 更新身分組 ###
     * @param int $wid 網站id
     * @param int $rid 身份組id
     * @param int $rInformation 身份組資訊
     * ```
     * [  
     * id=> Int  
     * wid=> Int  
     * name=> String  
     * displayname=> String  
     * parent_id=> Int  
     * permissions=> Int 
     * status=> Int 
     * ]
     * ```
     ************************************************/
    public function updateRoleInformation($wid, $rid, $rInformation = [], $autoCreateRole = false): bool
    {
        $result = true;
        $res = $this->getRoleInformation($wid, $rid, false);
        if (empty($res)) {
            if ($autoCreateRole) $result &= $this->addRoles($wid, $rid);
            else return false;
        }
        $rOriginalInformation = $res[0];
        // if (empty($rInformation['parent_id'])) {
        //     // $sql = "UPDATE `{$this->roles}` SET `parent_id` = ? WHERE `rid` = ?;";
        //     // $result &= $this->conn->prepare($sql, [$changePermission, $rid]);
        // }
        $params = [
            empty($rInformation['name']) ? $rOriginalInformation['name'] : $rInformation['name'],
            empty($rInformation['displayname']) ? $rOriginalInformation['displayname'] : $rInformation['displayname'],
            empty($rInformation['permissions']) ? $rOriginalInformation['permissions'] : $rInformation['permissions'],
            empty($rInformation['parent_id']) ? $rOriginalInformation['parent_id'] : $rInformation['parent_id'],
            empty($rInformation['status']) ? $rOriginalInformation['status'] : $rInformation['status'],
            $wid,
            $rid
        ];
        $sql = "UPDATE `{$this->roles}` SET `name` = ?, `displayname` =?, `permissions` = `permissions` ^ ?,`parent_id` = ?  WHERE `wid` = ? AND `id` = ?";
        $result &= empty($this->conn->prepare($sql, $params));
        return $result;
    }
}