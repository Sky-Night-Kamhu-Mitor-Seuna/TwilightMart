<?php

/****************************************************************************************
 * 
 * 資訊卡頁面
 * 
 * #2475b2 藍色
 * #b22424 紅色
 * #e45c10 橙色
 * #edc214 黃色
 * #1faa00 綠色
 * #7b23a7 紫色
 ****************************************************************************************/
// 引入的CSS及JS
$includeCss[] = "./css/" . WEBSITE['stylesheet'] . "/profileCard.css";
// $includeJs[] = "./javascripts/.js";
/****************************************************************************************/
// 預設顏色表
$colors = ['#3F769E', '#CC8200', '#9A71B5', '#EA6C6C'];
// 必須至少登入或者有附上mid
if (isset($_GET['mid']) || isset($_SESSION['mid'])) {
    $id = (isset($_GET['mid']) ? $_GET['mid'] : $_SESSION['mid']);
    // $isself = ($_GET['mid'] == $_SESSION['mid'] || !isset($_GET['mid']));
} else {
    header("location: ?route=login");
    exit;
}
// 檢查用戶是否存仔或者被停用
$sql = "SELECT mp.avatar, mp.introduction, mp.theme, m.nickname, m.account, mp.background 
FROM `" . CONFIG_TABLES['members_profile'] . "` AS mp 
JOIN `" . CONFIG_TABLES['members'] . "` AS m 
WHERE m.id = ? AND m.id = mp.mid AND `status` <> 0 ;";
$result = $db->prepare($sql, [$id]);
if (empty($result)) {
    header("location: ?route=err");
    exit;
}
// 如果有設置主題顏色則改用主題顏色，反之則使用隨機顏色
$memberColor = $result[0]['theme'] ? $result[0]['theme'] : $colors[array_rand($colors)];
// 如果有配置背景則使用背景
// if (!empty($result[0]['background'])) {
//     $newBodyImage = "background-image: linear-gradient(to top,
//     {$memberColor} .3), {$memberColor} .3)),
//     url('{$result[0]['background']}');";
//     $smarty->assign("newBodyImage", $newBodyImage);
// }
// 基本配置
$roles = $permissions->getMemberRoles($id, WEBSITE_ID);
foreach ($roles as $role) {
    $roleDisplayname[] = $role['displayname'];
}
$userInformation = array(
    "mid" => $id,
    'nickname' => $result[0]['nickname'],
    'avatar' => $result[0]['avatar'],
    'account' => $result[0]['account'],
    'introduction' => $result[0]['introduction'],
    'profileColor' => "{$memberColor}",
    'roles' => $roleDisplayname
    // 'isself' => $isself
);
$smarty->assign("user", $userInformation);
// $smarty->assign("avatar", file_exists("{$avatarPath}{$id}") ? "{$avatarPath}{$id}?t=" . crc32(time()) : "/assets/images/quaso.png");
// $smarty->assign("ProfileToolBarColor", "style='background-color: {$memberColor}  .7);'");
