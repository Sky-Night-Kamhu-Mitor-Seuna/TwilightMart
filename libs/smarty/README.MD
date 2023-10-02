## smarty
官方網站: [https://www.smarty.net](https://www.smarty.net/)  
快速安裝指南: [https://www.smarty.net/quick_install](https://www.smarty.net/quick_install)  
GitHub: [https://github.com/smarty-php/smarty](https://github.com/smarty-php/smarty)  
  
## smarty語法筆記
從外部引入設定值
```xml
{config_load file="test.conf" section="setup"}
```
----
從外部引入檔案
```xml
{include file="header.htm"}
```
假設於`header.htm`存在變數`title`也可代入值
```xml
{include file="header.htm" title=fooo}
```
----
變數
PHP變數
```php
//於 .php 配置
$smarty->assign("page","烘培機");
//於 templates/xxx.htm 配置
{$page}
```
設定值
```php
//於 .conf 配置
title = welcome to Smarty!
//於 templates/xxx.htm 配置
{#title#}
```
----
註解
```xml
{* bold and title are read from the config file *}
```
----
判斷式 如果`#bold#`為`True` 則印出 `<b>`
```xml
{if #bold#}<b>{/if}
```
----
不要進行翻譯  
_以下結果將直接輸出 `$Name|upper`_  
```xml
{ldelim}$Name|upper{rdelim}
```
