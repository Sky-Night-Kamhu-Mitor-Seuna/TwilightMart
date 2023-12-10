<?php

/****************************************************************************************
 * 
 * 登入系統元件
 * 
 ****************************************************************************************/
// 引入的CSS及JS
$includeCss[]="./css/".WEBSITE['stylesheet']."/login.css";
// $includeJs[] = "./javascripts/.js";
/****************************************************************************************/
// 如果已經登入將直接跳轉回會員頁面
if(isset($_SESSION['mid'])){
    $log->addSystemLog($sf->getId(), WEBSITE_ID,$_SESSION['mid'], $USER_IP_ADDRESS, "LOGIN", 0);
    header("location: ?route=member");
    exit;
}
if(isset($_POST['password']) && isset($_POST['account'])){
    $acc = strtolower(trim($_POST['account']));
    $pwd = hash("sha256",$_POST['password']);
    $_POST['password'] = '';
    $sql = "SELECT `nickname`, `id`, `status` FROM `".CONFIG_TABLES['members']."` WHERE `account` = ? AND `password` = ? LIMIT 1;";
    $result = $db->prepare($sql, [$acc, $pwd]);
    $smarty->assign("loginfail","LOGINFAIL");
    if(!empty($result)){
        if($result[0]['status'] == 0){
            $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['sessionId'], $USER_IP_ADDRESS, "LOGIN", 0);
            $smarty->assign("loginfail","ACCOUNT_DEACTIVATED");
            
        }else{
            $_SESSION["account"] = $acc;
            $_SESSION["mid"] = $result[0]['id'];
            $log->addSystemLog($sf->getId(), WEBSITE_ID, $result[0]['id'], $USER_IP_ADDRESS, "LOGIN", 1);
            $sql = "UPDATE `".CONFIG_TABLES['members']."` SET `last_ip_address` = ? WHERE `id` = ?;";
            $result = $db->prepare($sql, [$USER_IP_ADDRESS, $result[0]['id']]);
            // 跳轉回會員頁面
            header("location: ?route=member");
            exit;
        }
    }
    // 清空資訊並且回傳找不到帳號或者密碼
    $log->addSystemLog($sf->getId(), WEBSITE_ID, $_SESSION['sessionId'], $USER_IP_ADDRESS, "LOGIN", 0);
    $smarty->assign("account",$acc);
}

?>