<?php
/****************************************************************************************
 * 
 * 登入系統元件
 * 
 * 
****************************************************************************************/
$smarty->assign("check","OK");
// 如果是$_POST['submit']存在代表送出表單
if(isset($_POST['submit'])){
    $account = trim($_POST['account']);
    $pwd = hash("sha256",$_POST['password']);
    // 查找資料表是否有批配的帳號與密碼
    $sql = "SELECT `account`,`id` FROM {$CONFIG_WEB_DBNAME}.`{$CONFIG_TABLES['members']}` WHERE `account` = ? AND `password` = ? LIMIT 1;";
    $acc = $db->prepare($sql, [$account,$pwd]);
    if($acc[0]['account']){
        // 配置account帳號名稱及id
        $_SESSION["account"] = $acc[0]['account'];
        $_SESSION["mid"] = $acc[0]['id'];
        // 跳轉回會員頁面
        header("location: ?page=member");
        exit;
    }
    // 清空資訊並且回傳找不到帳號或者密碼
    $_POST['password'] = '';
    $smarty->assign("INPUTaccount",$account);
    $smarty->assign("check","LOGINFAIL");
}
// 如果已經登入將直接跳轉回會員頁面
if(isset($_SESSION['account'])){
    header("location: ?page=member");
    exit;
}
?>