<?php

/****************************************************************************************
 * 
 * 權限系統
 * 
 * 
 ****************************************************************************************/
class permissions
{
    private $conn;
    private $member_roles = "m_member_roles";
    private $role_permissions = "m_role_permissions";
    private $roles = "m_roles";
    /************************************************
     * ### permissions ###
     * @param obj $db 資料庫
     ************************************************/
    public function __construct($db)
    {
        $this->conn = $db;
    }
    /************************************************
     * ### 設置權限表 ###
     * @param obj $obj 資料表
     ************************************************/
    public function setTables($member_roles,$role_permissions,$roles)
    {
        $this->member_roles = $member_roles;
        $this->role_permissions = $role_permissions;
        $this->roles = $roles;
    }
    /************************************************
     * ### 確認使用者是否能存取物件 ###
     * @param int $mid 使用者id
     * @param int $permission 權限id 請傳入16進制值
     * @return bool 是否可存取
     ************************************************/
    public function checkMemberPermissions($mid, $permission)
    {
        $result = false;
        $roles=$this->checkMemberRoles($mid);
        foreach ( $roles as $role ){
            $permissions = $this->getRolePermissions($role);
            // if (in_array($permission, $permissions)||in_array(1, $permissions)) {
            //     $result = true;
            //     break;
            // }
            // 1 為最高權限
            if ( ($permission & $permissions) || (0x1 & $permissions)) {
                $result = true;
                break;
            }
        }
        return $result;
    }
    /************************************************
     * ### 確認使用者身份組 ###
     * @param int $mid 使用者id
     * @return array 使用者身份組
     ************************************************/
    private function checkMemberRoles($mid)
    {
        $result = array();
        $sql = "SELECT `role_id` FROM {$this->member_roles} WHERE member_id = ?";
        $roles = $this->conn->prepare($sql,[$mid]);
        foreach ($roles as $role){
            $result[] = $role['role_id'];
        }
        return $result;
    }
    /************************************************
     * ### 確認身份組權限 ###
     * @param int $gid 身份組id
     * @return int 權限(十六進制)
     ************************************************/
    private function getRolePermissions($gid)
    {
        // $permissions = array();
        $permissions = 0x0;
        $sql = "SELECT `permissions` FROM {$this->role_permissions} WHERE role_id = '{$gid}';";
        $rows = $this->conn->each($sql);
        $permissions = $rows['permissions'] | $permissions;

        // foreach ($rows as $row) {
        //     $permissions[] = $row['permissions'];
        // }

        $sql = "SELECT `parent_id`,`id` FROM {$this->roles} WHERE `id` = '{$gid}'";
        $row = $this->conn->single($sql);

        if ($row['parent_id']!=$row['id']) {
            $parentPermissions = $this->getRolePermissions($row['parent_id']);
            $permissions = $parentPermissions | $permissions;
            // $permissions = array_merge($permissions, $parentPermissions);
        }
        return $permissions;
    }
    /************************************************
     * ### 修改用戶身份組 ###
     * @param int $mid 使用者id
     * @param int $gid 身份組id
     ************************************************/
    public function changeMemberRoles($mid,$gid)
    {
        //還沒撰寫
    }
    /************************************************
     * ### 新增身份組 ###
     * @param int $gid 身份組id
     * @param array $permissions 權限
     ************************************************/
    public function addRoles($gid,$permissions)
    {
        //還沒撰寫
    }
    /************************************************
     * ### 移除身份組 ###
     * @param int $gid 身份組id
     ************************************************/
    public function deleteRoles($gid)
    {
        //還沒撰寫
    }
    /************************************************
     * ### 修改身份組 ###
     * @param int $gid 身份組id
     * @param string $name 新身份組名稱
     * @param string $displayName 新身份組暱稱
     * @param string $permissions 權限編輯項目
     ************************************************/
    public function editRoles($gid,$name,$displayName,$permissions)
    {
        //還沒撰寫
    }
}