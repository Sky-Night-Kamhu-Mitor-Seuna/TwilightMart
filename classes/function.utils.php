<?php
/*************************************************************
 * 
 * lastUpdate 02/27/2023
 * Function - 尚未分類
 * 
*************************************************************/
//防止xss攻擊
function cleanXss(&$string){
    return strip_tags( $string ,"<a><b><s><span><strong><em><br><h1><h2><h3><h4><h5><h6>");
}
function generateCRC32($input=null){
  return hash('crc32',microtime(true).$input);
  // return json_encode(array("result"=>hash('crc32',microtime(true))));
}
function generateUUID(){
  return uniqid();
}
/*
function generateUUID(){
    // 根據時間戳和隨機數字生成 UUID
    $uuid = sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
      mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000,
      mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
    return $uuid;
  }
*/
?>