<?php
/**
 * initSuperPermission下的键值KEY必须唯一，例如下面的-reply，menu，permission
 * 机构权限配置
 */
return [
    
        'spot' => [
            'categoryName' => '机构管理',
            'children' => [
                /* 病例模版 */
                [
                    'description' => '病例模板',
                    'name' => '/spot/case-template/index',
                ],
                [
                    'description' => '新增病例模板',
                    'name' => '/spot/case-template/create',
                ],
                [
                    'description' => '修改病例模板',
                    'name' => '/spot/case-template/update',
                ],
                [
                    'description' => '删除病例模板',
                    'name' => '/spot/case-template/delete',
                ],
                /* 检验医嘱 */
                [
                    'description' => '检验医嘱',
                    'name' => '/spot/inspect/index',
                ],
                [
                    'description' => '新增检验医嘱',
                    'name' => '/spot/inspect/create',
                ],
                [
                    'description' => '编辑检验医嘱',
                    'name' => '/spot/inspect/update',
                ],
                [
                    'description' => '查看检验医嘱',
                    'name' => '/spot/inspect/view',
                ],
                [
                    'description' => '删除检验医嘱',
                    'name' => '/spot/inspect/delete',
                ],
                [
                    'description' => '检验医嘱关联检验项目',
                    'name' => '/spot/inspect/union',
                ],
                /* 检验项目 */
                [
                    'description' => '检验项目',
                    'name' => '/spot/inspect-item/index',
                ],
                [
                    'description' => '新增检验项目',
                    'name' => '/spot/inspect-item/create',
                ],
                [
                    'description' => '查看检验项目',
                    'name' => '/spot/inspect-item/view',
                ],
                [
                    'description' => '编辑检验项目',
                    'name' => '/spot/inspect-item/update',
                ],
                [
                    'description' => '删除检验项目',
                    'name' => '/spot/inspect-item/delete',
                ],
                /* 检查医嘱 */
                [
                    'description' => '检查医嘱',
                    'name' => '/spot/check-list/index',
                ],
                [
                    'description' => '新增检查医嘱',
                    'name' => '/spot/check-list/create',
                ],
                [
                    'description' => '编辑检查医嘱',
                    'name' => '/spot/check-list/update',
                ],
                [
                    'description' => '查看检查医嘱',
                    'name' => '/spot/check-list/view',
                ],
                [
                    'description' => '删除检查医嘱',
                    'name' => '/spot/check-list/delete',
                ],
                
                
               
                
                /* 诊所管理begin */
                [
                    'description' => '诊所管理',
                    'name' => '/spot/index/index',
                ],
                [
                    'description' => '新增诊所',
                    'name' => '/spot/index/create',
                ],
                [
                    'description' => '查看诊所',
                    'name' => '/spot/index/view',
                ],
                [
                    'description' => '编辑诊所',
                    'name' => '/spot/index/update',
                ],
                [
                    'description' => '删除诊所',
                    'name' => '/spot/index/delete',
                ],
                
            ]            
           ],
];