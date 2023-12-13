<?php
require_once '../sys.global.php';
include_once 'new_page_data.php';
// 589605057335390208
echo "<title>測試用-快速建站" . ($sf->getId()) . "</title>";
if (!isset($_GET['test'])) {
	exit;
}
$wid = 1329099350638325762;
if (isset($_GET['web'])) {
	$domainName = htmlspecialchars($_GET['web']);
	$wid = $sf->getId();
	$sql = "INSERT INTO `s_website` (`id`, `domain`, `name`, `displayname`, `distribution`, `icon`, `background`) VALUES (?, ?, 'Demo', 'DemoWebsite', 'Taiwan', '/assets/images/logo.png', '/assets/images/bg.jpg');";
	$db->prepare($sql, [$wid, $domainName]);
	$sql = "INSERT INTO `s_newebpay` (`wid`,`store_prefix`,`store_id`,`store_hash_key`,`store_hash_iv`,`store_return_url`,`store_client_back_url`,`store_notify_url`) VALUES(?,'0','0','0','0','https://personal.snkms.com/projects/steam/','https://personal.snkms.com/projects/steam/','https://personal.snkms.com/projects/steam/api/done.php');";
	$db->prepare($sql, [$wid]);
}
// } else {
// 	echo "請輸入網站domainName變數\$_GET['web']";
// }

$sql = "SELECT `domain` FROM `s_website` WHERE `id` = ?";
$result = $db->prepare($sql, [$wid]);
$domainName = empty($result) ? '未知網站' : $result[0]['domain'];

$createPagesSql = "INSERT INTO `w_pages` (`id`, `wid`, `name`, `displayname`, `description`, `status`) VALUES ";
$createPageComponent = "INSERT INTO `w_page_component` (`id`, `pid`, `cid`, `displayname`, `position`, `params`, `permissions`, `status`) VALUES ";
foreach ($NEW_PAGE_INFORMATION as $pKey => $info) {
	$pid =  $sf->getId();
	$NEW_PAGE_INFORMATION[$pKey]['pid'] = $pid;
	foreach ($info['pageComponents'] as $cKey => $pageComponents) {
		if (!empty($pageComponents)) {
			$cid = $sf->getId();
			$createPageComponentSql[] =	$createPageComponent . "({$cid}, {$pid}, {$pageComponents['cid']}, '{$domainName}的{$pageComponents['displayname']}',  {$pageComponents['position']}, '{$pageComponents['params']}', {$pageComponents['permission']}, 1); ";
		}
	}
	$createPagesSql .=	"({$pid}, {$wid}, '{$info['name']}', '{$domainName}的{$info['displayname']}',  '{$info['description']}', {$info['status']})" . ($pKey < count($NEW_PAGE_INFORMATION) - 1 ? "," : ";") . " ";
}
echo '<span style="display:none;">' . $createPagesSql . '</span>';
$db->single($createPagesSql);
echo '<span style="display:none;">';
foreach ($createPageComponentSql as $sql) {
	echo $sql . '<br/>';
	$db->single($sql);
}
echo '</span>';
	// $db->single($createPageComponentSql);
