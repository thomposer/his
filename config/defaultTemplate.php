<?php
/**
 * 默认空白站点权限
 * 
 * 
/spot/sites/index,站点信息,1,0;
/module/admin/list,新增模块,1,0;
/module/admin/add,添加模块,0,0;
/rbac/assignment/index,用户管理,1,0;
/rbac/assignment/create,分配用户角色,0,0;
/rbac/assignment/update,更新用户,0,0;
/rbac/assignment/delete,删除用户,0,0;
/rbac/role/index,角色管理,1,0;
/rbac/role/create,创建角色,0,0;
/rbac/role/update,更新角色,0,0;
/rbac/role/delete,删除角色,0,0;
/rbac/permission/index,权限管理,1,0;
/rbac/permission/create,创建权限,0,0;
/rbac/permission/update,更新权限,0,0;
/rbac/permission/delete,删除权限,0,0;
 */
return [
	'spot' => [
		'categoryName' => '站点管理',
		'children' => [
			[
				'description' => '站点信息',
				'name' => '/spot/sites/index',
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
				'description' => '用户管理',
				'name' => '/rbac/assignment/index',
			],
			[
				'description' => '分配用户角色',
				'name' => '/rbac/assignment/create',
			],
			[
				'description' => '更新用户',
				'name' => '/rbac/assignment/update',
			],
			[
				'description' => '删除用户',
				'name' => '/rbac/assignment/delete',
			],
			[
				'description' => '角色管理',
				'name' => '/rbac/role/index',
			],
			[
				'description' => '创建角色',
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
// 			[
// 				'description' => '权限管理',
// 				'name' => '/rbac/permission/index',
// 			],
// 			[
// 				'description' => '创建权限',
// 				'name' => '/rbac/permission/create',
// 			],
// 			[
// 				'description' => '更新权限',
// 				'name' => '/rbac/permission/update',
// 			],
// 			[
// 				'description' => '删除权限',
// 				'name' => '/rbac/permission/delete',
// 			],
		]
	],
];