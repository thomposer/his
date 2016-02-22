<?php
/**
 * initSuperPermission下的键值KEY必须唯一，例如下面的-reply，menu，permission
 * 超级站点权限配置
 */
return [
    
    'initSuperPermission' => [
       
        
        'permission' => [
            'categoryName' => '权限管理',
            'children' => [
                'index' => [
                    'description' => '权限管理列表',
                    'name' => '/rbac/permission/index',
                ],
                'create' => [
                    'description' => '新建权限',
                    'name' => '/rbac/permission/create',
                ],
                'create_category' => [
                    'description' => '新建权限分类和站点权限',
                    'name' => '/rbac/permission/create_category',
                ],
                'update' => [
                    'description' => '更新权限',
                    'name' => '/rbac/permission/update',
                ],
                'delete' => [
                    'description' => '删除权限',
                    'name' => '/rbac/permission/delete',
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
        'apply' => [
            'categoryName' => '权限申请列表',
            'children' => [
                'index' => [
                    'description' => '权限申请列表',
                    'name' => '/rbac/apply/index',
                ],
                'allow' => [
                    'description' => '审批|冻结操作',
                    'name' => '/rbac/apply/allow',
                ],
                'delete' => [
                    'description' => '删除操作',
                    'name' => '/rbac/apply/delete',
                ],
             
            ]
        ],
        'wxinfo' => [
			'categoryName' => '站点管理',
			'children' => [
				'index' => [
					'description' => '站点列表',
					'name' => '/wxinfo/sites/index',
				],
				'create' => [
					'description' => '新增站点',
					'name' => '/wxinfo/sites/create',
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
		]
    ]
];