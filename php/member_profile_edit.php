<?php

/****************************************************************************************
 * 
 * 個人化頁面
 * 
 ****************************************************************************************/
// 引入的CSS及JS
$includeCss[] = "./css/" . WEBSITE['stylesheet'] . "/memberProfileEdit.css";
$includeJs[] = "./javascripts/uploadImage.js";
/****************************************************************************************/
// 沒有登入
if (!isset($_SESSION['mid'])) {
    header("location: ?route=login");
    exit;
}
// 確認用戶資訊
$sql = "SELECT `member`.`account`, `member`.`nickname`, `mProfile`.`avatar`, `mProfile`.`theme`
FROM `" . CONFIG_TABLES['members'] . "` AS `member` 
JOIN `" . CONFIG_TABLES['members_profile'] . "` AS `mProfile` ON `mProfile`.`mid` = `member`.`id`
WHERE `id` = ? LIMIT 1;";
$result = $db->prepare($sql, [$_SESSION['mid']]);
if (empty($result)) {
    header("location: ?route=login");
    exit;
}
$memberInformations = $result[0];
$nickname = $memberInformations['nickname'];
$account = $memberInformations['account'];
$avatar = $memberInformations['avatar'];
/****************************************************************************************/
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
/****************************************************************************************/
$smarty->assign("errorMessage", "NaN");
if (isset($_POST['passwordCheck']) && isset($_POST['account']) && isset($_POST['nickname'])) {
    // 確認帳號是否符合格式(至少3個字元且只接受 _ - @ # $ . )
    $nickname = htmlspecialchars($_POST['nickname']);
    $account = htmlspecialchars(strtolower($_POST['account']));
    if (checkAccount($account)) {
        $password = hash("sha256", $_POST['passwordCheck']);
        $sql = "SELECT `id` FROM `" . CONFIG_TABLES['members'] . "` WHERE `id` = ? AND `password` = ? LIMIT 1; ";
        $result = $db->prepare($sql, [$_SESSION['mid'], $password]);
        // 如果密碼不吻合，無法變更資訊
        if (!empty($result)) {
            if ($_POST['password'] != "") {
                if (!checkPassword($_POST['password'])) $smarty->assign("errorMessage", "PASSWORD_WRONG");
                else $password = hash("sha256", $_POST['password']);
                echo $_POST['password'];
            }
            $sql = "UPDATE `" . CONFIG_TABLES['members'] . "` SET `account` = ?, `nickname` = ?, `password` = ? WHERE `id` = ? LIMIT 1;";
            $result = $db->prepare($sql, [$account, $nickname, $password, $_SESSION['mid']]);

            $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['mid'], $USER_IP_ADDRESS, "CHANGE_MEMBER_INFORMATION", 1);
            $_SESSION["account"] = $account;
            $_SESSION["nickname"] = $nickname;
        } else $smarty->assign("errorMessage", "PASSWORD_NO_MATCH");
    } else $smarty->assign("errorMessage", "ACCOUNT_ERROR");
    // $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['mid'], $USER_IP_ADDRESS, "CHANGE_MEMBER_INFORMATION", 0);
    unset($_POST);
    // header("location: ?route=setting");
}
$smarty->assign("nickname", $nickname);
$smarty->assign("account", $account);
$smarty->assign("avatar", $avatar);
