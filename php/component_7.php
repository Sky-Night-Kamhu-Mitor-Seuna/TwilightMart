<?php
/****************************************************************************************
 * 
 * 會員頁面
 * 
 * #2475b2 藍色
 * #b22424 紅色
 * #e45c10 橙色
 * #edc214 黃色
 * #1faa00 綠色
 * #7b23a7 紫色
****************************************************************************************/
// 預設顏色表
$includeJs[]="./javascripts/uploadImage.js";
$colors = ['rgba(36, 117, 178,', 'rgba(178, 36, 36,', 'rgba(228, 92, 16,', 'rgba(31, 170, 0,', 'rgba(123, 35, 167,'];
$avatarPath = "assets/uploads/avatar/";
//必須至少登入或者有附上mid
if(isset($_GET['mid']) || isset($_SESSION['mid'])){
    $id=(isset($_GET['mid']) ? $_GET['mid'] : $_SESSION['mid']);
    $isself=($_GET['mid'] == $_SESSION['mid'] || !isset($_GET['mid']));
} else {
    header("location: ?page=login");
    exit;
}
// 檢查用戶是否存仔或者被停用
$sql = "SELECT mp.introduction, mp.theme, m.nickname, m.account, mp.background FROM {$CONFIG_WEB_DBNAME}.`{$CONFIG_TABLES['members_profile']}` AS mp JOIN {$CONFIG_WEB_DBNAME}.`{$CONFIG_TABLES['members']}` AS m WHERE m.id = ? AND m.id = mp.member_id AND `status` = 1 ;";
$result = $db->prepare($sql,[$id])[0];
if(!$result){
    header("location: ?page=err");
    exit;
}
// 如果有設置主題顏色則改用主題顏色，反之則使用隨機顏色
$memberColor=$result['theme']?$result['theme']:$colors[array_rand($colors)];
// 如果有配置背景則使用背景
if(!empty($result['background'])){
    $newBodyImage = "background-image: linear-gradient(to top,
    {$memberColor} .3), {$memberColor} .3)),
    url('{$result['background']}');";
    $smarty->assign("newBodyImage",$newBodyImage);
}
// 基本配置
$smarty->assign("mid",$id);
$smarty->assign("nickname",$result['nickname']);
$smarty->assign("account",$result['account']);
$smarty->assign("avatar",file_exists("{$avatarPath}{$id}")?"{$avatarPath}{$id}?t=".crc32(time()):"/assets/images/quaso.png");
$smarty->assign("introduction",$result['introduction']);
$smarty->assign("ProfileColor","style='background-color: {$memberColor} 1.0);'");
$smarty->assign("ProfileToolBarColor","style='background-color: {$memberColor}  .7);'");
$smarty->assign("isself",$isself);
/*****************************************************************
 * 判斷工具列使用權限
 * $tools 已授權之工具
 * $toolList 目前系統中存有的工具
 ****************************************************************/
$tools=[['title'=>'首頁', 'link'=>'home', 'component_id'=>14]];
$toolList=[
    ['pid'=>6, 'title'=>"商品調整", 'link'=>'productEdit', 'component_id'=>15],
    // ['pid'=>7, 'title'=>"商品分類", 'link'=>'productCategoryEdit', 'component_id'=>15],
    // ['pid'=>2, 'title'=>"群組管理", 'link'=>'groups', 'component_id'=>1],
    ['pid'=>11, 'title'=>"銷售統計", 'link'=>'salesReport', 'component_id'=>17]
];
// 預設被選取的工具固定為[1:首頁]
$toolAction=$tools[0]['link'];
// $authorized
if($isself){
    foreach ($toolList as $t){
        // 這裡直接決定了加載什麼頁面 checkMemberPermissions確認使用者是否有足夠的權限存取$toolList內的工具
        if($memberPermissions->checkMemberPermissions($id,$t['id'])){
            $tools[]=$t;
            // 判斷目前正在使用那項工具，如果找到則引入他
            if(isset($_GET['action'])&&($_GET['action']==$t['link'])){
                $toolAction=$t['link'];
                if (file_exists("php/component_{$t['component_id']}.php") && 
                include_once "php/component_{$t['component_id']}.php"){
                    $debugInfo.="<!--[php/component_{$t['component_id']}.php]-->\n";
                }
            }
        }
    }
}
$smarty->assign("toolAction",$toolAction);
$smarty->assign("tools",$tools);
?>