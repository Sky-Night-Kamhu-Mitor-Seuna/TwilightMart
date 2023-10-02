function checkpw($pwid,$pwcheckid,$pwhint) {
    $str = "";
    $value = document.getElementById($pwid).value;
    if ($value.length < 8) $str+="❎ 密碼長度低於八個字元\t\n";
    if (!$value.match(/[A-Z]/)) $str+="❎ 密碼缺少大寫字母\t\n";
    if (!$value.match(/[a-z]/)) $str+="❎ 密碼缺少小寫字母\t\n";
    if (!$value.match(/[0-9]/)) $str+="❎ 密碼缺少數字\t\n";
    if (!$value.match(/[!@#$%^&*()_\-+={[}\]:;"'<>,.?\/~`\\|]/)) $str+="❎ 密碼缺少特殊字元\t\n";
    if (document.getElementById($pwcheckid).value != $value) $str+="⛔ 與再次輸入密碼不符合\t\n";
    if ($str=="") $str="✅ 太棒了"
    changeinnerText($pwhint,$str)
} 