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
    private $role_permissions = "m_role_permissions";
    private $roles = "m_roles";
    private $permissions = "m_permissions";
    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 設置權限表 ###
     * @param string $member_roles
     * @param string $role_permissions
     * @param string $roles
     ************************************************/
    public function setTables($member_roles, $role_permissions, $roles) : bool
    {
        $this->member_roles = $member_roles;
        $this->role_permissions = $role_permissions;
        $this->roles = $roles;
        return true;
    }
    /************************************************
     * ### 確認使用者身份組 ###
     * @param int $mid 使用者id
     * @param int $wid 網站id
     ************************************************/
    private function checkMemberRoles($mid, $wid) : array
    {
        $result = array();
        $sql = "SELECT `rid` FROM {$this->member_roles} WHERE `mid` = ? AND `wid` = ?;";
        $roles = $this->conn->prepare($sql,[$mid, $wid]);
        foreach ($roles as $role){
            $result[] = $role['rid'];
        }
        return $result;
    }
    /************************************************
     * ### 確認身份組權限 ###
     * @param int $rid 身份組id
     * @param bool $containsParent 是否包含父身分組權限
     ************************************************/
    private function getRolePermissions($rid, $containsParent = true) : int
    {
        $permissions = 0x0;
        $sql = "SELECT `permissions` FROM {$this->role_permissions} WHERE `rid` = ?;";
        $rows = $this->conn->prepare($sql,[$rid]);
        // $rows = $this->conn->each($sql);
        $permissions |= $rows['permissions'];

        // 0 代表已刪除身分組
        $sql = "SELECT `parent_id`,`id` FROM {$this->roles} WHERE `id` = ? AND `status` <> 0;";
        $row = $this->conn->prepare($sql,[$rid]);
        // $row = $this->conn->single($sql);

        if ($row['parent_id']!=$row['id'] && $containsParent) {
            $parentPermissions = $this->getRolePermissions($row['parent_id']);
            $permissions |= $parentPermissions;
        }
        return $permissions;
    }
    /************************************************
     * ### 確認身分組是否存在 ###
     * @param int $rid 身份組id
     * @param int $wid 網站id
     ************************************************/
    private function checkRolesStatus($rid, $wid) : int
    {
        $sql = "SELECT `status` FROM `{$this->roles}` WHERE `id` = ? AND `wid` = ?;";
        $row = $this->conn->prepare($sql,[$rid,$wid]);
        $result = empty($row) ? 0: $row[0]['status'];
        return $result;
    }
    /************************************************
     * ### 確認使用者是否能存取物件 ###
     * @param int $mid 使用者id
     * @param int $wid 網站id
     * @param int $permission 權限id
     ************************************************/
    public function checkMemberPermissions($mid, $wid, $permission) : bool
    {
        $result = false;
        $roles=$this->checkMemberRoles($mid, $wid);
        foreach ( $roles as $role ){
            $permissions = $this->getRolePermissions($role);
            // 1 為最高權限
            if ( ($permission & $permissions) || (0x1 & $permissions)) {
                $result = true;
                break;
            }
        }
        return $result;
    }
    /************************************************
     * ### 修改用戶身份組 ###
     * @param int $mid 使用者id
     * @param int $rid 身份組id
     * @param int $wid 網站id
     * @param string $action 動作 `ADD`:增加 `DEL`:刪除
     ************************************************/
    public function changeMemberRoles($mid, $wid, $rid, $action="ADD") : bool
    {
        $memberHaveRole = in_array($rid, $this->checkMemberRoles($mid, $wid));
        switch($action){
            case "DEL":
            case "REM":
                if(!$memberHaveRole) return false;
                $sql = "DELETE FROM `{$this->member_roles}` WHERE `mid` = ? AND `rid` = ? AND `wid` = ?;";
                break;
            case "ADD":
            // default:
                if($memberHaveRole) return false;
                $sql = "INSERT INTO `{$this->member_roles}` (`mid`, `rid`, `wid`) VALUES (?, ?, ?);";
                break;
        }
        $rows = $this->conn->prepare($sql,[$mid, $rid, $wid]);

        return empty($rows);
    }
    /************************************************
     * ### 新增身份組 ###
     * @param int $gid 身份組id
     * @param array $permissions 權限
     ************************************************/
    public function addRoles($rid, $wid, $rName="New Role", $rDisplayname="New Role", $rParent=0) : bool
    {
        if($this->checkRolesStatus($rid,$wid)) return false;
        // 如果沒設置父身分組，則預設繼承為自己
        $rParent = $rParent != 0 ? $rParent : $rid;
        $sql = "INSERT INTO `{$this->roles}` (`id`, `wid`, `name`, `displayname`, `parent_id`) VALUES (?, ?, ?, ?, ?);";
        $rows = $this->conn->prepare($sql,[$rid, $wid, $rName, $rDisplayname, $rParent]);
        // 如果建立身分組失敗就跳出
        if(!empty($rows)) return false; 
        $sql = "INSERT INTO `{$this->role_permissions}` (`rid`, `permissions`) VALUES (?, 0) ;";
        $rows = $this->conn->prepare($sql,[$rid]);

        return empty($rows);
    }
    /************************************************
     * ### 移除身份組 ###
     * @param int $gid 身份組id
     ************************************************/
    public function deleteRoles($rid, $wid) : bool
    {
        if ($this->checkRolesStatus($rid,$wid) != 1 ) return false;
        $sql = "UPDATE `{$this->roles}` SET `status` = 0 WHERE `id` = ? AND `wid` = ?;";
        $rows = $this->conn->prepare($sql,[$rid, $wid]);

        return empty($rows);
    }
    /************************************************
     * ### 修改身份組名稱 ###
     * @param int $rid 身份組id
     * @param int $wid 網站id
     * @param string $rName 新身份組名稱
     * @param string $rDisplayname 新身份組暱稱
     ************************************************/
    public function editRolesName($rid, $wid, $rName, $rDisplayname) : bool
    {
        if(!$this->checkRolesStatus($rid, $wid)) return false;
        $sql = "UPDATE {$this->roles} SET `name`, `displayname` VALUE (?, ?) WHERE `rid` = ? AND `wid` = ?;";
        // :D
        $rDisplayname = $rName;
        $row = $this->conn->prepare($sql, [$rName, $rDisplayname, $rid, $wid]);

        return empty($rows);
    }
    /************************************************
     * ### 修改身份組權限 ###
     * @param int $rid 身份組id
     * @param int $wid 網站id
     * @param string $rPermissions 權限編輯項目
     * @param string $action 動作 `ADD`:增加 `DEL`:刪除
     ************************************************/
    public function editRolesPermission($rid, $wid, $rPermissions, $action="ADD") : bool
    {
        if(!$this->checkRolesStatus($rid, $wid)) return false;
        switch($action){
            case "DEL":
            case "REM":
                $sql = "UPDATE `{$this->role_permissions}` SET `permissions` = `permissions` | ? WHERE `rid` = ?;";
                break; 
            case "ADD":
            // default:
                $rPermissions = ~$rPermissions;
                $sql = "UPDATE `{$this->role_permissions}` SET `permissions` = `permissions` & ? WHERE `rid` = ?;";
                break;
        }
        
        $row = $this->conn->prepare($sql, [$rPermissions, $rid]);
        return empty($rows);
    }
    /************************************************
     * ### 修改身份組父身分組 ###
     * @param int $rid 身份組id
     * @param int $wid 網站id
     * @param string $rParent 編輯身分組父項目
     ************************************************/
    public function editRolesParent($rid, $wid, $rParentId) : bool
    {
        if(!$this->checkRolesStatus($rid, $wid)) return false;
        $sql = "UPDATE `{$this->roles}` SET `parent_id` = ? WHERE id = ? AND wid = ?;";
        
        $row = $this->conn->prepare($sql, [$rParentId, $rid, $wid]);
        return empty($rows);
    }
}