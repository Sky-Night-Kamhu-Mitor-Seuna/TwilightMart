<?php
require_once '../sys.global.php';
// 589605057335390208
echo "<title>測試".($sf->getId())."</title>";
// if(isset($_SESSION['id']))


print_r($pageRouter->getPageComponent(1329205667356995587));
// if($permissions->isAdmin(1328444050906279936, WEBSITE_ID)){
//     echo "done";
// }
// else{
//     echo "no";
// }

// echo hash("sha256", 1234);
// $a = new productManage($db);
// echo "-----------------------------<br/>";
// if($a->addProduct($sf->getId(),589605057335390208,"Hi",0.0,10)) echo "OK";
// else echo "NO";


// $db->debugmode(true);    
// $c->deletePageComponents($r->getId(),589605057335390211,1,"TEST")
// $c = new componentPage($db);
// if($c->addPageComponents($r->getId(),589605057335390214,1)){
//     echo "done";
// }else{
//     echo "no";
// }
// print_r($l->getLog(589605057335390208));

// echo $l->getLog(589605057335390208)[0]['COUNT(*)'];

// print_r($p->getRolePermissionsArray(589605057335390208));
// $l->addLog($r->getId(), 589605057335390208, 589605057335390208, "TEST測試".$r->getId(), 0);



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

// for($i = 1 ; $i < 3000 ; $i++){
//     $l->addSystemLog($r->getId(), 589605057335390208, 589605057335390208, $USER_IP_ADDRESS, "TEST測試".$r->getId(), 0);
//     $l->addViewLog($r->getId(), $TESTID, 5896085308993630212, $USER_IP_ADDRESS, $USER_AGENT);
// } 
// if($p->addRoles($r->getId(),589605057335390208)){
//     echo "done";
// }else{
//     echo "no";
// }
// print_r($p->getAllPermissionsArray());
?>
