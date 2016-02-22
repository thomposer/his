<?php
/**
 * initPermission下的键值KEY必须唯一，例如下面的-index,reply,menu
 * 普通站点权限配置
 */
return [
    
    'initPermission' => [
        
        'wxinfo' => [
            'categoryName' => '站点管理',
            'children' => [
                'index' => [
                    'description' => '站点信息',
                    'name' => '/wxinfo/sites/index',
                ], 
               'update' => [
			        'description' => '更新站点',
			        'name' => '/wxinfo/sites/update',
			    ],
			    'updateview' => [
			        'description' => '站点信息-token和url',
			        'name' => '/wxinfo/sites/updateview',
			    ],
                                 
            ]
        ],
       
        'reply' => [
            'categoryName' => '自动回复',
            'children' => [
                'beadded' => [
                    'description' => '被添加自动回复',
                    'name' => '/manage/reply/beadded',
                ],
                'auto' => [
                    'description' => '消息自动回复',
                    'name' => '/manage/reply/auto',
                ],
                'smarty' => [
                    'description' => '关键词自动回复',
                    'name' => '/manage/reply/smarty',
                ],
                'material_list' => [
                    'description' => '获取素材列表',
                    'name' => '/api/material/list',
                ],
                'material_get' => [
                	'description' => '获取素材',
                	'name' => '/api/material/get',
                ],
                'material_news' => [
                	'description' => '获取图片',
                	'name' => '/api/material/news',
                ],
                'reply_event_update' => [
                	'description' => '自动回复更新',
                	'name' => '/api/reply-event/update',
                ],
                'reply_event_delete' => [
                	'description' => '自动回复删除',
                	'name' => '/api/reply-event/delete',
                ],
                'rule_add' => [
                	'description' => '添加回复规则',
                	'name' => '/api/rule/add',
                ],
                'rule_del' => [
                	'description' => '删除回复规则',
                	'name' => '/api/rule/del',
                ],
                'rule_update' => [
                	'description' => '更新回复规则',
                	'name' => '/api/rule/update',
                ],
            ]
        ],
       
         'menu' => [
            'categoryName' => '自定义菜单',
            'children' => [
                'index' => [
                    'description' => '自定义菜单列表',
                    'name' => '/manage/menu/index', 
                ],
                'allow' => [
                    'description' => '更新菜单',
                    'name' => '/manage/menu/update',
                ],
                'delete' => [
                    'description' => '删除菜单',
                    'name' => '/manage/menu/delete',
                ],
               
            ]
        ], 

        'role' => [
            'categoryName' => '角色管理',
            'children' => [
                'index' => [
                    'description' => '角色管理列表',
                    'name' => '/rbac/role/index',
                ],
                'create' => [
                    'description' => '新建角色',
                    'name' => '/rbac/role/create',
                ],
                'update' => [
                    'description' => '更新角色',
                    'name' => '/rbac/role/update',
                ],
                'delete' => [
                    'description' => '删除角色',
                    'name' => '/rbac/role/delete',
                ],
               
            ]
        ],
        'assignment' => [
            'categoryName' => '用户管理',
            'children' => [
                'index' => [
                    'description' => '用户管理列表',
                    'name' => '/rbac/assignment/index',
                ],
                'create' => [
                    'description' => '新建用户',
                    'name' => '/rbac/assignment/create',
                ],
                'update' => [
                    'description' => '更新用户',
                    'name' => '/rbac/assignment/update',
                ],
                'delete' => [
                    'description' => '删除用户',
                    'name' => '/rbac/assignment/delete',
                ],
               
            ]
        ],

        'star' => [
            'categoryName' => '明星权限管理',
            'children' => [
                'index' => [
                    'description' => '管理员列表',
                    'name' => '/star/admin/index',
                ],
                'view' => [
                    'description' => '管理员信息详情',
                    'name' => '/star/admin/view',
                ],
                'update' => [
                    'description' => '修改管理员信息',
                    'name' => '/star/admin/update',
                ],
                'delete' => [
                    'description' => '删除管理员信息',
                    'name' => '/star/admin/delete',
                ],
                'create' => [
                    'description' => '添加管理员',
                    'name' => '/star/admin/create',
                ],
            ]
        ],
    ]
];