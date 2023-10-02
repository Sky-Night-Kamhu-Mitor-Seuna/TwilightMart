## ☆ TwilightMart
系統環境  
- [RockyOS](https://rockylinux.org/zh_TW/news/rocky-linux-8-6-ga-release/)/8.6  
- [Apache](https://httpd.apache.org/)/2.4.53  
- [PHP](https://www.php.net/)/8.0.20  
- [composer](https://getcomposer.org/)/2.5.4  
- ~~[codeigniter](https://codeigniter.tw/)/4.1.5~~  

**安裝Apache**  
```shell
sudo dnf install httpd -y
```
**安裝Composer**  
```shell
sudo dnf install composer -y

cd /var/www/html/  
composer install  
```
**目錄**
```diff
$ tree -L 2
.
├── 404                 // 404 Not found 頁面
├── api                 // API 
│   └── NewebPay        // 藍新金流
│   └── upload          // 檔案上傳
├── assets              // 素材資源
│   └── images          // 圖片素材
│   └── uploads         // 上傳檔案
├── cache               // 快取
├── classes             // 類別庫
├── configs             // 設定值
│   └── config.ini      // 一些基礎設定值(重要)
├── css                 // Css樣式
│   └── style_default   // 默認頁面設計
├── inc.global.php      // 全域配置
├── index.php           // 主頁
├── javascripts         // Js腳本
├── libs                // 函式庫
│   └── smarty          // Smarty函式庫
├── LICENSE             // MIT LICENSE
├── php                 // 頁面物件
├── plugins             // Smarty Plugin
├── sqlscripts          // 資料庫腳本
├── src                 // 不顯示在前後臺的背景執行php
├── templates           // 前端設計
│   └── style_default   // 頁面物件設計
└── templates_c         // 前端快取
```
- - -
_當前版本快照_  
![](assets/Template1.png)
- - -
> ### 使用資源  
> * _lib/[Smarty 4.3.0](https://www.smarty.net)_  
> * _javaScript/[Bootstrap v4.0.0](https://getbootstrap.com)_  
> * _javaScript/[jquery-3.2.1](https://jquery.com)_  
> * _javaScript/[Popper 3](https://github.com/vusion/popper.js)_  
> * _css/[Bootstrap v4.0.0](https://getbootstrap.com)_  
