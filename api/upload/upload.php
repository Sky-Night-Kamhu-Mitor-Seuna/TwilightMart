<?php
require_once '../../classes/class.file.php';
require_once '../../classes/class.fake404.php';
exit();
$fileUpload = new files();
// $_FILES['IMAGE_UPLOAD']['error'] === UPLOAD_ERR_OK
if(isset($_FILES['IMAGE_UPLOAD']) && isset($_POST['IMAGE_RENAME'])){
    switch($_POST['IMAGE_TYPE']){
        case"USER_AVATAR":
            if(preg_match('/^[a-zA-Z0-9_\-@$.\s]+$/', $_POST['IMAGE_RENAME'])){
                $fileUpload->setPath("avatar");
                $result = $fileUpload->upload($_FILES['IMAGE_UPLOAD'],$_POST['IMAGE_RENAME']);
                exit($result);
            }else exit("Failed, data error.");
            break;
        case"PRODUCT_IMAGE":
            $fileUpload->setPath(hash('md5',$_SERVER['SERVER_NAME'])."/products");
            $result = $fileUpload->upload($_FILES['IMAGE_UPLOAD'],$_POST['IMAGE_RENAME']);
            exit($result);
            break;
        default:exit("Failed, unacceptable type.");
    }
}
if (strpos($_SERVER['HTTP_USER_AGENT'], 'curl') !== false) {echo "[Warning] Client is trying to hijack {$_SERVER['SERVER_NAME']}\t\n"; return ;}
?>