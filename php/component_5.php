<?php
/****************************************************************************************
 * 
 * 註冊系統元件
 * 
 ****************************************************************************************/
// 引入的CSS及JS
$includeCss[] = "./css/" . WEBSITE['stylesheet'] . "/login.css";
$includeJs[] = "./javascripts/checkpasswd.js";
/****************************************************************************************/
$smarty->assign("errorMessage", "NaN");
// 檢查密碼
function checkPassword($password)
{
    if (strlen($password) < 8) return false;
    if (!preg_match('/[A-Z]/', $password)) return false;
    if (!preg_match('/[a-z]/', $password)) return false;
    if (!preg_match('/[0-9]/', $password)) return false;
    if (!preg_match('/[!@#$%^&*()_\-+={}\[\]:;"\'<>,.?\/~`|\\\]/', $password)) return false;
    return true;
}
// 檢查帳號
function checkAccount($account)
{
    if (strlen($account) < 3) return false;
    if (!preg_match('/^[a-zA-Z0-9_\-@$.\s]+$/', $account)) return false;
    return true;
}

if (isset($_POST['password']) && isset($_POST['account']) && isset($_POST['nickname'])) {
    $nickname = htmlspecialchars($_POST['nickname']);
    $account = htmlspecialchars(strtolower($_POST['account']));
    
    $smarty->assign("nickname", $nickname);
    $smarty->assign("account", $account);
    // 確認帳號是否符合格式(至少3個字元且只接受 _ - @ # $ . )
    if (checkAccount($account)) {
        // 確認密碼是否為複雜性密碼(至少8個字元且有包含大寫及小寫數字及特殊符號)
        if (checkPassword($_POST['password'])) {
            // 再次輸入密碼是否為正確的
            if ($_POST['password'] === $_POST['passwordCheck']) {
                $pwd = hash("sha256", $_POST['password']);
                $sql = "SELECT `account` FROM `".CONFIG_TABLES['members']."` WHERE `account`=? LIMIT 1;";
                $acc = $db->prepare($sql, [$account]);
                // 判斷帳號是否重註冊 (如果資料庫撈的到資料代表有註冊)
                if (!$acc) {
                    // 產生一個id透過帳號進行加鹽
                    $id = $sf->getId();
                    $sql = "INSERT INTO `".CONFIG_TABLES['members']."` (`id`, `account`, `password`, `nickname`, `last_ip_address`) VALUES (?, ?, ?, ?, ?);";
                    $db->prepare($sql, [$id, $account, $pwd, $nickname, $USER_IP_ADDRESS]);
                    $sql = "INSERT INTO `".CONFIG_TABLES['members_profile']."` (`mid`) VALUES (?);";
                    $db->prepare($sql, [$id]);
                    $rEveryoneId = $permissions->getRoleEveryoneId(WEBSITE_ID);
                    $rEveryoneId = $rEveryoneId == 0 ? 589605057335390208 : $rEveryoneId ;
                    $sql = "INSERT INTO `".CONFIG_TABLES['member_roles']."` (`wid`, `mid`, `rid`) VALUES (".WEBSITE_ID.", ?, ?);";
                    $db->prepare($sql, [$id, $rEveryoneId]);
                    $log->addSystemLog($sf->getId(), WEBSITE_ID, $id, $USER_IP_ADDRESS, "REGISTER", 1);
                    // 配置account帳號名稱及id
                    $_SESSION["account"] = $account;
                    $_SESSION["mid"] = $id;
                    // 跳轉回會員頁面
                    header("location: ?page=member");
                    exit;
                } else {
                    // $log->addSystemLog($sf->getId(), WEBSITE_ID, $id, $USER_IP_ADDRESS, "REGISTER", 0);
                    $smarty->assign("errorMessage", "ACCOUNT_EXISTS");
                }
            } else {
                $smarty->assign("errorMessage", "PASSWORD_NO_MATCH");
            }
        } else {
            $smarty->assign("errorMessage", "PASSWORD_WRONG");
        }
    } else {
        $smarty->assign("errorMessage", "ACCOUNT_ERROR");
    }
    $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['sessionId'], $USER_IP_ADDRESS, "REGISTER", 0);
}

// 如果已經登入將直接跳轉回會員頁面
if (isset($_SESSION['account'])) {
    header("location: ?page=member");
    exit;
}
