<?php

/*************************************************************
 * 
 * 通用Function
 * 
 *************************************************************/
/************************************************
 * ### 產生CRC32 ###
 * @param string $account 帳號
 ************************************************/
function generateCRC32($input = null) : string
{
    return hash('crc32', microtime(true) . $input);
}
/************************************************
 * ### 檢查密碼格式是否符合複雜性密碼原則 ###
 * @param string $password 密碼
 ************************************************/
function checkPassword($password) : bool
{
    if (strlen($password) < 8) return false;
    if (!preg_match('/[A-Z]/', $password)) return false;
    if (!preg_match('/[a-z]/', $password)) return false;
    if (!preg_match('/[0-9]/', $password)) return false;
    if (!preg_match('/[!@#$%^&*()_\-+={}\[\]:;"\'<>,.?\/~`|\\\]/', $password)) return false;
    return true;
}
/************************************************
 * ### 檢查帳戶格式 ###
 * @param string $account 帳號
 ************************************************/
function checkAccount($account) : bool
{
    if (strlen($account) < 3) return false;
    if (!preg_match('/^[a-zA-Z0-9_\-@$.]+$/', $account)) return false;
    return true;
}
