<?php
/****************************************************************************************
 * 
 * 註冊系統元件
 * 
 * 
****************************************************************************************/
$includeJs[]="./javascripts/checkpasswd.js";
$smarty->assign("check","OK");
// 檢查密碼
function checkPassword($password) {
    if (strlen($password) < 8) return false;
    if (!preg_match('/[A-Z]/', $password)) return false;
    if (!preg_match('/[a-z]/', $password)) return false;
    if (!preg_match('/[0-9]/', $password)) return false;
    if (!preg_match('/[!@#$%^&*()_\-+={}\[\]:;"\'<>,.?\/~`|\\\]/', $password)) return false;
    return true;
}
// 檢查帳號
function checkAccount($account) {
    if (strlen($account) < 3) return false;
    if (!preg_match('/^[a-zA-Z0-9_\-@$.\s]+$/', $account)) return false;
    return true;
}
// 如果是$_POST['submit']存在代表送出表單
if (isset($_POST['submit'])) {
    $nickname = htmlspecialchars($_POST['nickname']);
    $account = htmlspecialchars($_POST['account']);
    $smarty->assign("INPUTnickname",$nickname);
    $smarty->assign("INPUTaccount",$account);
    // 確認帳號是否符合格式(至少3個字元且只接受 _ - @ # $ . )
    if(checkAccount($account)){
        // 確認密碼是否為複雜性密碼(至少8個字元且有包含大寫及小寫數字及特殊符號)
        if(checkPassword($_POST['passwd'])){
            // 再次輸入密碼是否為正確的
            if($_POST['passwd'] === $_POST['passwordCheck']){
                $pwd = hash("sha256", $_POST['passwd']);
                $sql = "SELECT `account` FROM {$CONFIG_WEB_DBNAME}.`{$CONFIG_TABLES['members']}` WHERE `account`=? LIMIT 1;";
                $acc = $db->prepare($sql, [$account]);
                // 判斷帳號是否重註冊 (如果資料庫撈的到資料代表有註冊)
                if (!$acc) {
                    // 產生一個id透過帳號進行加鹽
                    $id = generateCRC32($account);
                    $sql = "INSERT INTO {$CONFIG_WEB_DBNAME}.`{$CONFIG_TABLES['members']}` (`id`, `account`, `password`, `nickname`) VALUES (?, ?, ?, ?);";
                    $db->prepare($sql, [$id, $account, $pwd, $nickname]);
                    // $sql = "INSERT INTO `m_roles` (`name`, `displayname`, `parent_id`) VALUES (?, '用戶個人群組', 1);";
                    // $db->prepare($sql, [$account]);
                    // $sql = "SELECT `account`,`id` FROM {$CONFIG_WEB_DBNAME}.`{$CONFIG_TABLES['members']}` WHERE `id`=? LIMIT 1;";
                    // $acc = $db->prepare($sql, [$id]);
                    $sql = "INSERT INTO {$CONFIG_WEB_DBNAME}.`{$CONFIG_TABLES['members_profile']}` (`member_id`) VALUES ('{$id}');";
                    $db->single($sql);
                    $sql = "INSERT INTO `{$CONFIG_TABLES['member_roles']}` (`member_id`, `role_id`) VALUES ('{$id}','ac68d651');";
                    $db->single($sql);
                    // 配置account帳號名稱及id
                    $_SESSION["account"] = $account;
                    $_SESSION["mid"] = $id;
                    // 跳轉回會員頁面
                    header("location: ?page=member");
                    exit;
                } else { $smarty->assign("check","ACC_EXISTS"); }
            }else{ $smarty->assign("check","PWD_ERROR2"); }
        } else { $smarty->assign("check","PWD_ERROR1"); }
    } else { $smarty->assign("check","ACC_ERROR"); }
}
// 如果已經登入將直接跳轉回會員頁面
if(isset($_SESSION['account'])){
    header("location: ?page=member");
    exit;
}
?>
