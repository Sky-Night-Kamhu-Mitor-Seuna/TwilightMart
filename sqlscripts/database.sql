-- -------------------------------------------------------
-- lastUpdate 05/07/2023
-- 核心資料表
-- By MaizuRoad
-- -------------------------------------------------------
CREATE DATABASE `Mai_Websites`;
USE `Mai_Websites`;

-- 網站資訊
CREATE TABLE IF NOT EXISTS `s_website` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `domain` varchar(20) NOT NULL COMMENT '網域名稱',
  `name` varchar(20) NOT NULL COMMENT '網站名稱',
  `displayname` varchar(18) NOT NULL COMMENT '網站別名',
  `distribution` varchar(32) DEFAULT 'Taiwan' NOT NULL  COMMENT '地理位置',
  `dbname` varchar(18) NOT NULL COMMENT '使用資料庫',
  `icon` varchar(255) DEFAULT NULL COMMENT '圖示',
  `background` varchar(255) DEFAULT NULL COMMENT '背景',
  `stylesheet` varchar(255) DEFAULT 'style_default' NOT NULL COMMENT '主題',
  `theme` varchar(255) DEFAULT '2475b2' NOT NULL COMMENT '色系',
  `status` int(3) NOT NULL DEFAULT 1 COMMENT '啟用狀態1啟用 0關閉',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  PRIMARY KEY (`id`)
) COMMENT='網站資訊';

-- 會員資料表
CREATE TABLE IF NOT EXISTS `m_members` (
  `id` VARCHAR(8) NOT NULL COMMENT 'ID',
  `account` varchar(20) NOT NULL COMMENT '唯一識別項帳號',
  `nickname` varchar(18) NOT NULL COMMENT '暱稱',
  `password` varchar(255) NOT NULL COMMENT '密碼',
  `status` int(3) NOT NULL DEFAULT 1 COMMENT '帳號狀態1啟用 0關閉',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  PRIMARY KEY (`id`)
) COMMENT='會員資料表';

-- 會員頁面資料表
CREATE TABLE IF NOT EXISTS `m_members_profile` (
  `member_id`  VARCHAR(8) NOT NULL COMMENT 'ID',
  `introduction` text NOT NULL DEFAULT '這個人很懶，什麼都沒寫' COMMENT '個人介紹',
  `theme` varchar(7) DEFAULT NULL COMMENT '顏色風格',
  `background` varchar(255) DEFAULT NULL COMMENT '自訂背景',
  PRIMARY KEY (`member_id`),
  CONSTRAINT `fk_m_members`
    FOREIGN KEY (`member_id`)
    REFERENCES `m_members` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) COMMENT='會員資料表';

-- 網站元件
CREATE TABLE IF NOT EXISTS `s_components` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '元件ID',
  `name` VARCHAR(50) NOT NULL COMMENT '元件名稱',
  `description` TEXT COMMENT '元件描述',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  PRIMARY KEY (id)
) COMMENT='元件資料表';

-- 藍新支付
CREATE TABLE IF NOT EXISTS `s_newebpay` (
  `id` INT NOT NULL COMMENT '網站ID',
  `store_prefix` VARCHAR(3) NOT NULL COMMENT '商店代號(三碼)',
  `store_id` VARCHAR(12) NOT NULL COMMENT '商店ID',
  `store_hash_key` VARCHAR(32) NOT NULL COMMENT '商店雜湊KEY',
  `store_hash_iv` VARCHAR(16) NOT NULL COMMENT '商店雜湊IV',
  `store_return_url` VARCHAR(255) NOT NULL COMMENT '交易完成返回網址',
  `store_client_back_url` VARCHAR(255) DEFAULT 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' NOT NULL COMMENT '交易完成客端返回網址',
  `store_notify_url` VARCHAR(255) NOT NULL COMMENT '通知網址',
  PRIMARY KEY (id),
  CONSTRAINT `fk_s_website`
    FOREIGN KEY (`id`)
    REFERENCES `s_website` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) COMMENT='藍新支付';

-- 權限組
-- 這個表只用來標示沒有其他用途
CREATE TABLE IF NOT EXISTS `m_permissions` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'ID 第幾個位元(由右至左)',
  `name` VARCHAR(100) NOT NULL COMMENT '權限名稱',
  `displayname` VARCHAR(100) NOT NULL COMMENT '權限顯示名稱',
  PRIMARY KEY (id)
) COMMENT='權限資料表';

-- 建立Demo站台
INSERT INTO `s_website` (`domain`, `name`, `displayname`, `distribution`, `dbname`, `icon`, `background`) VALUES ('localhost', 'Demo', 'Demo Website', 'Taiwan', 'demoshop', '/assets/images/logo.png', '/assets/images/bg.jpg');

-- 插入Demo藍新
INSERT INTO `s_newebpay` (`id`,`store_prefix`,`store_id`,`store_hash_key`,`store_hash_iv`,`store_return_url`,`store_client_back_url`,`store_notify_url`) VALUES(1,'0','0','0','0','https://personal.snkms.com/projects/steam/','https://personal.snkms.com/projects/steam/','https://personal.snkms.com/projects/steam/api/done.php');

-- 插入權限
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('*', '所有權限');
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('group', '群組管理');
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('website', '網站基本資料維護');
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('menu', '選單維護');
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('page', '頁面維護');
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('product_create', '商品建檔');
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('product_category', '商品分類');
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('order_view', '訂單查詢');
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('order_manage', '訂單管理');
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('order_customer_service', '訂單客服');
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('sales_report', '銷售統計報表');
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('post_create', '張貼貼文');
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('post_manage', '管理貼文');
INSERT INTO `m_permissions` (`name`, `displayname`) VALUES ('product_review', '評價商品');

-- 新增元件
INSERT INTO `s_components` (`name`,`description`) VALUES ("error","404");
INSERT INTO `s_components` (`name`,`description`) VALUES ("ann","公告");
INSERT INTO `s_components` (`name`,`description`) VALUES ("signup","創建帳號");
INSERT INTO `s_components` (`name`,`description`) VALUES ("login","登入");
INSERT INTO `s_components` (`name`,`description`) VALUES ("logout","登出");
INSERT INTO `s_components` (`name`,`description`) VALUES ("adblock","廣告");
INSERT INTO `s_components` (`name`,`description`) VALUES ("memberInfo","會員資料");
INSERT INTO `s_components` (`name`,`description`) VALUES ("storeList","商店");
INSERT INTO `s_components` (`name`,`description`) VALUES ("product","商品");
INSERT INTO `s_components` (`name`,`description`) VALUES ("paymentCheck1","交易確認1");
INSERT INTO `s_components` (`name`,`description`) VALUES ("paymentCheck2","交易確認2");
INSERT INTO `s_components` (`name`,`description`) VALUES ("paymentFinal","交易完成");
INSERT INTO `s_components` (`name`,`description`) VALUES ("productRec","商品推薦");
INSERT INTO `s_components` (`name`,`description`) VALUES ("memberHome","用戶首頁");
INSERT INTO `s_components` (`name`,`description`) VALUES ("edit","一般調整");
INSERT INTO `s_components` (`name`,`description`) VALUES ("about","說明頁面");

-- 插入會員 root 密碼：P@55word
INSERT INTO `m_members` (`id`, `account`, `nickname`, `password`) VALUES ('67618368', 'root', 'Administrator', '3fbfeb0ee307127bbd4ef7da33f7b57a9ff3c7357da182c5bfccc2a4f599c6f9');
INSERT INTO `m_members_profile` (`member_id`) VALUES ('67618368');

-- -------------------------------------------------------
-- lastUpdate 05/07/2023
-- 商店資料表
-- By MaizuRoad
-- -------------------------------------------------------
CREATE DATABASE `demoshop`;
USE `demoshop`;
-- 導覽列
CREATE TABLE IF NOT EXISTS `s_navbar` (
  `id` INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
  `name` VARCHAR(50) NOT NULL,
  `displayname` VARCHAR(50) NOT NULL,
  `link` VARCHAR(255) NOT NULL
) COMMENT='導覽列';

-- 網站頁面
CREATE TABLE IF NOT EXISTS `s_pages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT '頁面ID',
  `title` VARCHAR(100) NOT NULL COMMENT '頁面標題',
  `link` VARCHAR(100) NOT NULL COMMENT '連結',
  `image` VARCHAR(100) NOT NULL DEFAULT "/assets/images/logo" COMMENT '圖示',
  `description` TEXT COMMENT '頁面描述',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  PRIMARY KEY (id)
) COMMENT='頁面資料表';

-- 網站頁面應用元件
CREATE TABLE IF NOT EXISTS `s_component_page` (
  `uuid` CHAR(13) NOT NULL COMMENT 'UUID',
  `displayname` VARCHAR(128) NOT NULL COMMENT '名稱',
  `component_id` INT(11) NOT NULL COMMENT '元件ID',
  `page_id` INT(11) NOT NULL COMMENT '頁面ID',
  `position` INT(11) NOT NULL COMMENT '位置',
  `params` JSON NOT NULL COMMENT '元件參數',
  `permissons` VARBINARY(50) NOT NULL DEFAULT '0x0' COMMENT '瀏覽該頁面需求權限0x0代表無須權限0x2代表需要群組管理權限',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  CONSTRAINT `fk_component_id` 
      FOREIGN KEY (`component_id`)
      REFERENCES `Mai_Websites`.`s_components`(`id`)
      ON DELETE CASCADE 
      ON UPDATE CASCADE,
  CONSTRAINT `fk_page_id` 
      FOREIGN KEY (`page_id`)
      REFERENCES `s_pages`(`id`)
      ON DELETE CASCADE 
      ON UPDATE CASCADE,
  PRIMARY KEY (`uuid`)
) COMMENT='元件與頁面關聯表';

-- 身份組
-- 跟會員帳號一樣獨立
CREATE TABLE IF NOT EXISTS `m_roles` (
  `id` VARCHAR(8) NOT NULL COMMENT 'ID',
  `name` VARCHAR(100) NOT NULL COMMENT '身份組名稱',
  `displayname` VARCHAR(100) NOT NULL COMMENT '身份組顯示名稱',
  `parent_id` INT(11) NULL COMMENT '父身份組',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  PRIMARY KEY (`id`)
) COMMENT='角色資料表';

-- 會員擁有的身份組
CREATE TABLE IF NOT EXISTS `m_member_roles` (
  `member_id` VARCHAR(8) NOT NULL COMMENT '會員ID',
  `role_id` VARCHAR(8) NOT NULL COMMENT '身份組',
  KEY (`member_id`, `role_id`),
  CONSTRAINT `member_roles_fk_role_id`
    FOREIGN KEY (`role_id`)
    REFERENCES `m_roles` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `member_roles_fk_member_id`
    FOREIGN KEY (`member_id`)
    REFERENCES `Mai_Websites`.`m_members` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) COMMENT='會員資料表';

-- 身份組權限
-- role_id 對應 身份組
-- permission_id 對應 權限組
CREATE TABLE IF NOT EXISTS `m_role_permissions` (
  `role_id` varchar(8) NOT NULL COMMENT '身份組ID',
  `permissions` VARBINARY(50) NOT NULL DEFAULT '0x0' COMMENT '權限表每個01對應著是否具有權限0x1代表擁有所有權限',
  CONSTRAINT `fk_role_id` 
    FOREIGN KEY (`role_id`)
    REFERENCES `m_roles`(`id`)
    ON DELETE CASCADE 
    ON UPDATE CASCADE,
  CONSTRAINT `fk_permission_id`
    FOREIGN KEY (`permission_id`)
    REFERENCES `Mai_Websites`.`m_permissions`(`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE
) COMMENT='角色權限關聯表';

-- 商品分類
CREATE TABLE IF NOT EXISTS `i_product_types` (
    `id` VARCHAR(8) NOT NULL COMMENT '分類ID',
    `name` VARCHAR(255) NOT NULL COMMENT '分類名稱',
    `description` TEXT COMMENT '分類描述',
    `image_url` VARCHAR(255) COMMENT '分類圖片URL',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    PRIMARY KEY (`id`)
) COMMENT='商品資料表';

-- 商品頁面
CREATE TABLE IF NOT EXISTS `i_products` (
    `id` varchar(8) NOT NULL COMMENT '商品ID',
    `name` VARCHAR(255) NOT NULL COMMENT '商品名稱',
    `price` DECIMAL(10, 2) NOT NULL COMMENT '商品價格',
    `description` TEXT COMMENT '商品描述',
    `type_id` VARCHAR(8) COMMENT '分類ID',
    `quantity` INT NOT NULL DEFAULT 0 COMMENT '商品數量',
    `status` TINYINT(3) NOT NULL DEFAULT 0 COMMENT '商品狀態：0-刪除;1-上架;2-下架',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
    CONSTRAINT `fk_type_id`
    FOREIGN KEY (`type_id`)
    REFERENCES `i_product_types`(`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    PRIMARY KEY (`id`)
) COMMENT='商品資料表';

-- 訂單資訊
-- (0)未付款
-- (100)完成付款，系統自動完成 (101)取消交易，客戶取消 (102)逾期未付款
-- (200)完成交易，後台人為按壓 (201)取消交易，後台取消 (102)後台訂單刪除
CREATE TABLE IF NOT EXISTS `p_orders` (
  `id` char(16) NOT NULL COMMENT '訂單ID',
  `member_id` VARCHAR(8) NOT NULL COMMENT '會員ID',
  `amount` DECIMAL(10, 2) NOT NULL COMMENT '交易金額',
  `status` VARCHAR(10) NOT NULL DEFAULT 0 COMMENT '付款狀態：0-未付款；100-完成付款；101-取消交易；102-逾期未付款；200-完成交易；201-取消交易；202-後台訂單刪除',
  `payment_method` VARCHAR(50) NOT NULL DEFAULT 'DEF_藍新金流' COMMENT '付款方式',
  `hash` char(32) NOT NULL COMMENT '雜湊驗證碼',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '創建時間',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新時間',
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_member_id` 
    FOREIGN KEY (`member_id`)
    REFERENCES `Mai_Websites`.`m_members`(`id`)
    ON DELETE CASCADE 
    ON UPDATE CASCADE
) COMMENT='訂單資訊表';

-- 訂單明細
CREATE TABLE IF NOT EXISTS `p_order_items` (
    `id` INT PRIMARY KEY AUTO_INCREMENT COMMENT '訂單商品關聯ID',
    `order_id` char(16) NOT NULL COMMENT '訂單ID',
    `product_id` VARCHAR(8) NOT NULL COMMENT '商品ID',
    `quantity` INT NOT NULL COMMENT '商品數量',
    `price` DECIMAL(10, 2) NOT NULL COMMENT '商品價格',
    CONSTRAINT `fk_order_id` 
      FOREIGN KEY (`order_id`)
      REFERENCES `p_orders`(`id`)
      ON DELETE CASCADE 
      ON UPDATE CASCADE,
    CONSTRAINT `fk_product_id` 
      FOREIGN KEY (`product_id`)
      REFERENCES `i_products`(`id`)
      ON DELETE CASCADE 
      ON UPDATE CASCADE
) COMMENT='訂單商品明細表';

-- 插入基本身份組
INSERT INTO `m_roles` (`id`, `name`, `displayname`, `parent_id`) VALUES ('ac68d651', 'everyone', '所有人', 1);
INSERT INTO `m_roles` (`id`, `name`, `displayname`, `parent_id`) VALUES ('ea068e21', 'root', '超級管理員', 1);
INSERT INTO `m_roles` (`id`, `name`, `displayname`, `parent_id`) VALUES ('448af5fc', 'admin', '管理員', 1);
INSERT INTO `m_role_permissions` (`role_id`, `permissions`) VALUES ('ea068e21', 0x0001);
INSERT INTO `m_role_permissions` (`role_id`, `permissions`) VALUES ('448af5fc', 0x1FFC);
INSERT INTO `m_role_permissions` (`role_id`, `permissions`) VALUES ('ac68d651', 0x2000);

-- 插入最高權限用戶帳戶權限
INSERT INTO `m_member_roles` (`member_id`, `role_id`) VALUES ('67618368','ea068e21');

-- 插入導覽列連結
INSERT INTO `s_navbar` (`name`, `displayname`, `link`) VALUES ("Home", "首頁", "?page=home");
INSERT INTO `s_navbar` (`name`, `displayname`, `link`) VALUES ("Store", "商店", "?page=store");
INSERT INTO `s_navbar` (`name`, `displayname`, `link`) VALUES ("About", "關於我們", "?page=about");

-- 插入頁面
INSERT INTO `s_pages` (`title`, `link`, `description`) VALUES ("錯誤","err","錯誤頁面");
INSERT INTO `s_pages` (`title`, `link`, `description`) VALUES ("首頁","home","網站的首頁");
INSERT INTO `s_pages` (`title`, `link`, `description`) VALUES ("註冊","signup","註冊頁面");
INSERT INTO `s_pages` (`title`, `link`, `description`) VALUES ("登入","login","登入頁面");
INSERT INTO `s_pages` (`title`, `link`, `description`) VALUES ("登出","logout","登出頁面");
INSERT INTO `s_pages` (`title`, `link`, `description`) VALUES ("帳戶","member","用戶頁面");
INSERT INTO `s_pages` (`title`, `link`, `description`) VALUES ("管理","admweb","管理員頁面");
INSERT INTO `s_pages` (`title`, `link`, `description`) VALUES ("商店","store","商店頁面");
INSERT INTO `s_pages` (`title`, `link`, `description`) VALUES ("商品","product","商品詳細頁面");
INSERT INTO `s_pages` (`title`, `link`, `description`) VALUES ("付款","check1_payment","購買確認頁面");
INSERT INTO `s_pages` (`title`, `link`, `description`) VALUES ("付款","check2_payment","結帳頁面");
INSERT INTO `s_pages` (`title`, `link`, `description`) VALUES ("付款","final_payment","結帳完成頁面");
INSERT INTO `s_pages` (`title`, `link`, `description`) VALUES ("關於","about","關於我們");

-- 新增頁面元件 
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644ca9015aa7e","錯誤",1,1,0,"{}");
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644cd28baef13","公告",2,2,0,"{}");
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644ca8d7df45e","登入畫面",4,4,0,"{}");
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644ca90d10e45","登入畫面下方廣告",6,4,1,"{}");
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644ca8f622e1f","註冊畫面",3,3,0,"{}");
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644e182ab2dfe","註冊畫面下方廣告",6,3,1,"{}");
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644ca9075ca91","商店頁面",8,8,0,"{}");
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644e183396307","商店畫面下方廣告",6,8,1,"{}");
-- INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644e181304d8d","錯誤",1,6,0,"{}");
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644e181f66018","商品詳細頁面",9,9,0,"{}");
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644e5771b5cb1","登出",5,5,0,"{}");
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ('644e967d568f3','交易確認頁面', 10, 10, 0, '{}');
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ('644ea1803fbc9','交易確認頁面2', 11, 11, 0, '{}');
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ('644ea1884a1ce','交易完成頁面', 12, 12, 0, '{}');
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ('644fdeeb5ab0f','會員系統頁面', 7, 6, 0, '{}');
-- INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ('644fdf015947f','調整商品頁面', 14, 6, 0, '{}');
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644fe0fd85604","商品詳細頁面下方推薦",13,9,1,"{}");
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644fe1bc403f8","交易確認頁面下方推薦",13,10,1,"{}");
INSERT INTO `s_component_page` (`uuid`, `displayname`, `component_id`, `page_id`, `position`, `params`) VALUES ("644fe1bc40399","關於",16,13,1,"{}");

-- 插入商品 及 商品分類
INSERT INTO `i_product_types` (`id`, `name`, `description`) VALUES ('c8862d23', '未分類', '沒有分類');
INSERT INTO `i_products` (`id`, `name`, `price`, `description`, `type_id`, `quantity`, `status`) VALUES ('a439c883', '測試垮鬆', '5.00', '好ㄔ的垮鬆', 'c8862d23', -1, '1');
INSERT INTO `i_products` (`id`, `name`, `price`, `description`, `type_id`, `quantity`, `status`) VALUES ('abf004aa', '測試垮鬆(草莓口味)', '6.00', '戀愛的ㄗ味', 'c8862d23', 0, '1');
INSERT INTO `i_products` (`id`, `name`, `price`, `description`, `type_id`, `quantity`, `status`) VALUES ('ee046789', '測試垮鬆(巧克力口味)', '6.00', '開心的港覺', 'c8862d23', 13, '1');
INSERT INTO `i_products` (`id`, `name`, `price`, `description`, `type_id`, `quantity`, `status`) VALUES ('593f9891', '測試垮鬆(芒果口味)', '8.00', '夏天ㄉ味道', 'c8862d23', 6, '1');
INSERT INTO `i_products` (`id`, `name`, `price`, `description`, `type_id`, `quantity`, `status`) VALUES ('e1228c0f', '測試垮鬆(紅豆口味)', '6.00', '思鄉惹', 'c8862d23', 7, '1');
INSERT INTO `i_product_types` (`id`, `name`, `description`) VALUES ('0d94a6e3', '測試', 'Bonjour!!');
INSERT INTO `i_products` (`id`, `name`, `price`, `description`, `type_id`, `quantity`, `status`) VALUES ('ef675ad6', '測試垮鬆(祕密特別版)', '8.00', '不能跟別人縮', '0d94a6e3', 5, '1');
INSERT INTO `i_products` (`id`, `name`, `price`, `description`, `type_id`, `quantity`, `status`) VALUES ('1d5c7e92', '測試垮鬆(法國正統版本)', '15.00', 'Bonjour', '0d94a6e3', 999, '1');