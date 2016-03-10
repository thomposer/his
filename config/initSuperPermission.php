<?php
/**
 * initSuperPermission下的键值KEY必须唯一，例如下面的-reply，menu，permission
 * 超级站点权限配置
 */
return [
    
        'spot' => [
            'categoryName' => '系统设置',
            'children' => [
                [
                    'description' => '站点信息',
                    'name' => '/spot/sites/index',
                ],
                [
                    'description' => '模块列表',
                    'name' => '/module/admin/index',
                ],
                [
                    'description' => '新增模块',
                    'name' => '/module/admin/list',
                ],
                [
                    'description' => '添加模块',
                    'name' => '/module/admin/add',
                ],
                [
                    'description' => '初始化模块',
                    'name' => '/module/admin/create',
                ],
                [
                    'description' => '更新模块',
                    'name' => '/module/admin/update',
                ],
                [
                    'description' => '模块详情',
                    'name' => '/module/admin/view',
                ],
                
                [
                    'description' => '用户管理',
                    'name' => '/rbac/apply/index',
                ],
                [
                    'description' => '添加用户',
                    'name' => '/rbac/apply/create',
                ],
                [
                    'description' => '更新用户',
                    'name' => '/rbac/apply/update',
                ],
                [
                    'description' => '删除用户',
                    'name' => '/rbac/apply/delete',
                ],
                [
                    'description' => '角色管理',
                    'name' => '/rbac/role/index',
                ],
                [
                    'description' => '添加角色',
                    'name' => '/rbac/role/create',
                ],
                [
                    'description' => '更新角色',
                    'name' => '/rbac/role/update',
                ],
                [
                    'description' => '删除角色',
                    'name' => '/rbac/role/delete',
                ],
                [
                    'description' => '权限管理',
                    'name' => '/rbac/permission/index',
                ],
                [
                    'description' => '添加权限',
                    'name' => '/rbac/permission/create',
                ],
                [
                    'description' => '更新权限',
                    'name' => '/rbac/permission/update',
                ],
                [
                    'description' => '删除权限',
                    'name' => '/rbac/permission/delete',
                ],
                [
                    'description' => '添加权限分类',
                    'name' => '/rbac/permission/create_category',
                ],
                             
                [
                    'description' => '菜单管理',
                    'name' => '/module/menu/index',
                ],
                [
                    'description' => '添加菜单',
                    'name' => '/module/menu/create',
                ],
                [
                    'description' => '更新菜单',
                    'name' => '/module/menu/update',
                ],
                [
                    'description' => '查看菜单',
                    'name' => '/module/menu/view',
                ],
                [
                    'description' => '删除菜单',
                    'name' => '/module/menu/delete',
                ],
                [
                    'description' => '行为日志记录',
                    'name' => '/behavior/record/index',
                ],
                [
                    'description' => '查看日志记录',
                    'name' => '/behavior/record/view',
                ],
                [
                    'description' => '删除日志记录',
                    'name' => '/behavior/record/delete',
                ],
                [
                    'description' => '删除前一个月日志记录',
                    'name' => '/behavior/record/deletemonth',
                ]
                
            ]
            
           ]
];