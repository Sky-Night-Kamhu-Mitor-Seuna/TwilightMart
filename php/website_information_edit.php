<?php

/****************************************************************************************
 * 
 * 網站編輯頁面
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
$displayname = WEBSITE_NAME;
$distribution = WEBSITE_DISTRIBUTION;
$icon = WEBSITE_ICON_URL;
/****************************************************************************************/
$smarty->assign("errorMessage", "NaN");
if (isset($_POST['passwordCheck']) && isset($_POST['displayname']) && isset($_POST['distribution'])) {
    $displayname = htmlspecialchars($_POST['displayname']);
    $distribution = htmlspecialchars(strtolower($_POST['distribution']));
    $password = hash("sha256", $_POST['passwordCheck']);
    $sql = "SELECT `id` FROM `" . CONFIG_TABLES['members'] . "` WHERE `id` = ? AND `password` = ? LIMIT 1; ";
    $result = $db->prepare($sql, [$_SESSION['mid'], $password]);
    // 如果密碼不吻合，無法變更資訊
    if (!empty($result)) {
        $sql = "UPDATE `" . CONFIG_TABLES['website'] . "` SET `displayname` = ?, `distribution` = ? WHERE `id` = ? ;";
        $result = $db->prepare($sql, [$displayname, $distribution, WEBSITE_ID]);
        $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['mid'], $USER_IP_ADDRESS, "CHANGE_WEBSITE_INFORMATION", 1);
    } else $smarty->assign("errorMessage", "PASSWORD_NO_MATCH");
    // $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['mid'], $USER_IP_ADDRESS, "CHANGE_WEBSITE_INFORMATION", 0);
    unset($_POST);
    // header("location: ?route=setting");
}
$smarty->assign("displayname", $displayname);
$smarty->assign("distribution", $distribution);
$smarty->assign("icon", $icon);
