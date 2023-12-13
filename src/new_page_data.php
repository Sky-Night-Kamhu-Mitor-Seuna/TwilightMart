<?php
$NEW_PAGE_INFORMATION = [
    [
        "pid" => 0,
        "name" => 'err',
        "displayname" => '錯誤資源頁面',
        "description" => '找不到任何東西，真遺憾...',
        "status" => '2',
        "pageComponents" => [
            [
                "cid" => 1,
                "displayname" => "There is nothing we can do.",
                "params" => '{"errorCode":"404","message":"找不到任何資源"}',
                "position" => 0,
                "permission" => 0
            ]
        ]
    ],
    [
        "pid" => 0,
        "name" => 'home',
        "displayname" => '首頁',
        "description" => '烘培雞',
        "status" => '2',
        "pageComponents" => [
            [
                "cid" => 2,
                "displayname" => "首頁導覽列",
                "params" => '[{"displayname":"首頁","link":"?route=home","icon":"e88a"},{"displayname":"商店","link":"?route=store","icon":"e051"},{"displayname":"關於","link":[{"displayname":"關於團隊","link":"?route=about","icon":"e7ef"},{"displayname":"免責聲明","link":"?route=disclaimer","icon":"f04c"},{"displayname":"版本訊息","link":"?jump=https://github.com/Sky-Night-Kamhu-Mitor-Seuna/TwilightMart","icon":"f04c"}]}]',
                "position" => 0,
                "permission" => 0
            ],
            [
                "cid" => 3,
                "displayname" => "首頁橫幅(上)",
                "params" => '{"style":0,"image":"/assets/images/webdesign.svg","message":["Hello Im MaizuRoad "],"button":[{"displayname":"see more>>","link":"?route=home"}]}',
                "position" => 1,
                "permission" => 0
            ],
            [
                "cid" => 3,
                "displayname" => "首頁橫幅(下)",
                "params" => '{"style":1,"image":["/assets/images/24HR.svg","/assets/images/SRRVICE.svg"],"message":["Your most trusted shopping platform","您最值得信賴的購物平台"]}',
                "position" => 2,
                "permission" => 0
            ]
        ]
    ],
    [
        "pid" => 0,
        "name" => 'login',
        "displayname" => '登入',
        "description" => '立刻登入我們的網站',
        "status" => '2',
        "pageComponents" => [
            [
                "cid" => 4,
                "displayname" => "登入",
                "params" => '',
                "position" => 0,
                "permission" => 0
            ]
        ]
    ],
    [
        "pid" => 0,
        "name" => 'logout',
        "displayname" => '登出',
        "description" => '如果你看到這行字，代表你網路爆炸了',
        "status" => '2',
        "pageComponents" => [
            [
                "cid" => 6,
                "displayname" => "登出",
                "params" => '',
                "position" => 0,
                "permission" => 0
            ]
        ]
    ],
    [
        "pid" => 0,
        "name" => 'register',
        "displayname" => '註冊',
        "description" => '立刻加入我們~~',
        "status" => '2',
        "pageComponents" => [
            [
                "cid" => 5,
                "displayname" => "註冊",
                "params" => '',
                "position" => 0,
                "permission" => 0
            ]
        ]
    ],
    [
        "pid" => 0,
        "name" => 'member',
        "displayname" => '帳戶',
        "description" => '關於用戶資訊',
        "status" => '2',
        "pageComponents" => [
            [
                "cid" => 2,
                "displayname" => "帳戶導覽列",
                "params" => '[{"displayname":"首頁","link":"?route=home","icon":"e88a"},{"displayname":"商店","link":"?route=store","icon":"e051"},{"displayname":"設定","link":[{"displayname":"個人化","link":"?route=setting","icon":"f02e"},{"displayname":"說明","link":"?route=disclaimer","icon":"e887"}]}]',
                "position" => 0,
                "permission" => 0
            ], [
                "cid" => 7,
                "displayname" => "帳戶資訊卡",
                "params" => '',
                "position" => 1,
                "permission" => 0
            ]
        ]
    ],
    [
        "pid" => 0,
        "name" => 'store',
        "displayname" => '商店',
        "description" => '痾...商店',
        "status" => '2',
        "pageComponents" => [
            [
                "cid" => 2,
                "displayname" => "商店導覽列",
                "params" => '[{"displayname":"首頁","link":"?route=home","icon":"e88a"},{"displayname":"商店","link":"?route=store","icon":"e051"},{"displayname":"關於","link":[{"displayname":"關於團隊","link":"?route=about","icon":"e7ef"},{"displayname":"免責聲明","link":"?route=disclaimer","icon":"f04c"},{"displayname":"版本訊息","link":"?jump=https://github.com/Sky-Night-Kamhu-Mitor-Seuna/TwilightMart","icon":"f04c"}]}]',
                "position" => 0,
                "permission" => 0
            ], [
                "cid" => 9,
                "displayname" => "商品清單",
                "params" => '',
                "position" => 1,
                "permission" => 0
            ]
        ]
    ],
    [
        "pid" => 0,
        "name" => 'product',
        "displayname" => '商品',
        "description" => '很高興你對我們的商品感興趣',
        "status" => '2',
        "pageComponents" => [
            []
        ]
    ],
    [
        "pid" => 0,
        "name" => 'about',
        "displayname" => '關於',
        "description" => '關於我們的團隊',
        "status" => '2',
        "pageComponents" => [
            []
        ]
    ],
    [
        "pid" => 0,
        "name" => 'admin',
        "displayname" => '管理員頁面',
        "description" => '沒有權限',
        "status" => '2',
        "pageComponents" => [
            [
                "cid" => 2,
                "displayname" => "管理員導覽列",
                "params" => '[{"displayname":"網站管理","link":[{"displayname":"站台管理","link":"?route=site-management"},{"displayname":"頁面管理","link":"?route=page-management"},{"displayname":"金流管理","link":"?route=payment-management"},{"displayname":"報表工具","link":"?route=report-tool"}]},{"displayname":"商品管理","link":[{"displayname":"商品管理","link":"?route=product-management"},{"displayname":"分類管理","link":"?route=category-management"}]},{"displayname":"群組管理","link":[{"displayname":"身分組管理","link":"?route=role-management"},{"displayname":"會員管理","link":"?route=member-management"}]},{"displayname":"訂單管理","link":[{"displayname":"查詢訂單","link":"?route=order-query"},{"displayname":"訂單編輯","link":"?route=order-edit"}]}]',
                "position" => 0,
                "permission" => 0
            ], [
                "cid" => 10,
                "displayname" => "管理員首頁",
                "params" => '',
                "position" => 1,
                "permission" => 0
            ]
        ]
    ],
    [
        "pid" => 0,
        "name" => 'setting',
        "displayname" => '個人化',
        "description" => '為自己打造個性化的配置',
        "status" => '2',
        "pageComponents" => [
            [
                "cid" => 2,
                "displayname" => "會員個人化導覽列",
                "params" => '[{"displayname":"首頁","link":"?route=home","icon":"e88a"},{"displayname":"商店","link":"?route=store","icon":"e051"},{"displayname":"設定","link":[{"displayname":"個人化","link":"?route=setting","icon":"f02e"},{"displayname":"說明","link":"?route=disclaimer","icon":"e887"}]}]',
                "position" => 0,
                "permission" => 0
            ], [
                "cid" => 11,
                "displayname" => "會員個人化",
                "params" => '{"method":"member-setting"}',
                "position" => 1,
                "permission" => 0
            ]
        ]
    ],
    [
        "pid" => 0,
        "name" => 'site-management',
        "displayname" => '網站編輯工具',
        "description" => '沒有權限',
        "status" => '2',
        "pageComponents" => [
            [
                "cid" => 2,
                "displayname" => "管理員導覽列",
                "params" => '[{"displayname":"網站管理","link":[{"displayname":"站台管理","link":"?route=site-management"},{"displayname":"頁面管理","link":"?route=page-management"},{"displayname":"金流管理","link":"?route=payment-management"},{"displayname":"報表工具","link":"?route=report-tool"}]},{"displayname":"商品管理","link":[{"displayname":"商品管理","link":"?route=product-management"},{"displayname":"分類管理","link":"?route=category-management"}]},{"displayname":"群組管理","link":[{"displayname":"身分組管理","link":"?route=role-management"},{"displayname":"會員管理","link":"?route=member-management"}]},{"displayname":"訂單管理","link":[{"displayname":"查詢訂單","link":"?route=order-query"},{"displayname":"訂單編輯","link":"?route=order-edit"}]}]',
                "position" => 0,
                "permission" => 0
            ], [
                "cid" => 12,
                "displayname" => "網站編輯工具",
                "params" => '{"method":"website-management"}',
                "position" => 1,
                "permission" => 0
            ]
        ]
    ],
    [
        "pid" => 0,
        "name" => 'product-management',
        "displayname" => '商品編輯工具',
        "description" => '沒有權限',
        "status" => '2',
        "pageComponents" => [
            [
                "cid" => 2,
                "displayname" => "商品管理導覽",
                "params" => '[{"displayname":"網站管理","link":[{"displayname":"站台管理","link":"?route=site-management"},{"displayname":"頁面管理","link":"?route=page-management"},{"displayname":"金流管理","link":"?route=payment-management"},{"displayname":"報表工具","link":"?route=report-tool"}]},{"displayname":"商品管理","link":[{"displayname":"商品管理","link":"?route=product-management"},{"displayname":"分類管理","link":"?route=category-management"}]},{"displayname":"群組管理","link":[{"displayname":"身分組管理","link":"?route=role-management"},{"displayname":"會員管理","link":"?route=member-management"}]},{"displayname":"訂單管理","link":[{"displayname":"查詢訂單","link":"?route=order-query"},{"displayname":"訂單編輯","link":"?route=order-edit"}]}]',
                "position" => 0,
                "permission" => 0
            ], [
                "cid" => 13,
                "displayname" => "商品管理",
                "params" => '',
                "position" => 1,
                "permission" => 0
            ]
        ]
    ]
];
?>