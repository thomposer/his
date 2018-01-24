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
	'make_appointment' => [
		'categoryName' => '预约管理',
		'children' => [		
		    [
              'description' => '预约管理',
			  'name' => '/make_appointment/patient/index',
		    ],
		    [
		      'description' => '添加预约',
		      'name' => '/make_appointment/patient/create',
		    ],
		    [
		      'description' => '查看预约',
		      'name' => '/make_appointment/patient/view',
		    ],
		    [
		      'description' => '更新预约',
		      'name' => '/make_appointment/patient/update',
		    ],
		    [
		      'description' => '删除预约',
		      'name' => '/make_appointment/patient/delete',
		    ],		
		]
	],
        
];