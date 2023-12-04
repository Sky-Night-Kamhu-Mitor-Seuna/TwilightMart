<?php
require_once '../sys.global.php';
// 589605057335390208

$r = new snowflake();
echo "<title>測試".($r->getId())."</title>";


// $db->debugmode(true);    
// $c->deletePageComponents($r->getId(),589605057335390211,1,"TEST")
// $c = new componentPage($db);
// if($c->addPageComponents($r->getId(),589605057335390214,1)){
//     echo "done";
// }else{
//     echo "no";
// }
$pm = new pageManage($db);
$p = new permissions($db);
$l = new syslog($db);
// print_r($l->getLog(589605057335390208));

// echo $l->getLog(589605057335390208)[0]['COUNT(*)'];
$p = new permissions($db);
// print_r($p->getRolePermissionsArray(589605057335390208));
// $l->addLog($r->getId(), 589605057335390208, 589605057335390208, "TEST測試".$r->getId(), 0);
$j=1;



// $a = $pm->findPageInformation($testId);
// foreach($a as $aa){
//     echo $aa['id']."<br/>";
// }
// while(1){
//     $i=$l->getSystemLog(589605057335390208,$j);
//     echo $j."<br/>";
//     if(empty($i)) break;
//     print_r($i);
//     $j++;
// }

for($i = 1 ; $i < 3000 ; $i++){
    $l->addSystemLog($r->getId(), 589605057335390208, 589605057335390208, $USER_IP_ADDRESS, "TEST測試".$r->getId(), 0);
    $l->addViewLog($r->getId(), $TESTID, 5896085308993630212, $USER_IP_ADDRESS, $USER_AGENT);
} 
// if($p->addRoles($r->getId(),589605057335390208)){
//     echo "done";
// }else{
//     echo "no";
// }
// print_r($p->getAllPermissionsArray());
?>
