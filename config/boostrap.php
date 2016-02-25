<?php
/**
 * 定义文件目录路径别名
 */
Yii::setAlias('@RootPath', dirname(__DIR__));
Yii::setAlias('@PublicPath',"@RootPath/public");
Yii::setAlias('@JsPath', "@PublicPath/js");
Yii::setAlias('@CssPath',"@PublicPath/css");
Yii::setAlias('@ImgPath', "@PublicPath/img");
Yii::setAlias("@DepPath","@RootPath/dep");
Yii::setAlias("@DepPath","@RootPath/gulp");
Yii::setAlias("@DepPath","@RootPath/node_modules");

/**
 * 临时文件夹
 */
Yii::setAlias('@TempPath', '@RootPath/temp');

/**
 * 文件服务器目录
 */
Yii::setAlias('@filePath', '@PublicPath/assets');

/**
 * 定义访问路径别名
 */

//RBAC权限管理模块
Yii::setAlias('@rbacAssignment', '/rbac/assignment/index');
Yii::setAlias('@rbacAssignmentCreate', '/rbac/assignment/create');
Yii::setAlias('@rbacAssignmentUpdate', '/rbac/assignment/update');
Yii::setAlias('@rbacAssignmentDelete', '/rbac/assignment/delete');
Yii::setAlias('@rbacRole', '/rbac/role/index');
Yii::setAlias('@rbacRoleCreate', '/rbac/role/create');
Yii::setAlias('@rbacRoleUpdate', '/rbac/role/update');
Yii::setAlias('@rbacRoleDelete', '/rbac/role/delete');
Yii::setAlias('@rbacPermission', '/rbac/permission/index');
Yii::setAlias('@rbacPermissionCreate', '/rbac/permission/create');
Yii::setAlias('@rbacPermissionUpdate', '/rbac/permission/update');
Yii::setAlias('@rbacPermissionDelete', '/rbac/permission/delete');
Yii::setAlias('@rbacApplyIndex', '/rbac/apply/index');
Yii::setAlias('@rbacApplyPass', '/rbac/apply/pass');
Yii::setAlias('@rbacApplyDelete', '/rbac/apply/delete');
Yii::setAlias('@rbacApplyUpdate', '/rbac/apply/update');
//申请权限和添加站点模块
Yii::setAlias('@applyApplyCreate', '/apply/apply/create');
Yii::setAlias('@applyApplyIndex', '/apply/apply/index');
Yii::setAlias('@applyApplyWxcreate', '/apply/apply/wxcreate');

Yii::setAlias('@rbacRule', '/rbac/rule/index');

//站点选择模块
Yii::setAlias('@manage', '/manage');
Yii::setAlias('@manageDefaultIndex', '/manage/default/index');
Yii::setAlias('@manageIndex', '/manage/index/index');
Yii::setAlias('@manageSites', '/manage/sites/index');
Yii::setAlias('@manageLogout', '/manage/sites/logout');

// 站点管理模块
Yii::setAlias('@spotSitesIndex', '/spot/sites/index');
Yii::setAlias('@spotSitesCreate', '/spot/sites/create');
Yii::setAlias('@spotSitesUpdate', '/spot/sites/update');
Yii::setAlias('@spotSitesTemplate', '/spot/sites/template');
Yii::setAlias('@spotSitesList', '/spot/sites/list');

//系统管理员角色别名
Yii::setAlias('@systemRole', 'systems');//角色名
Yii::setAlias('@systemPermission', 'allpermissions');//权限名

//站点角色前缀
Yii::setAlias('@spotPrefix', 'spot_');

Yii::setAlias('@TemplateIndex', '/template/index/index');
Yii::setAlias('@TemplateIndexCreate', '/template/index/create');

//模板管理模块
Yii::setAlias('@moduleAdminCreate', '/module/admin/create');
Yii::setAlias('@moduleAdminIndex', '/module/admin/index');
Yii::setAlias('@moduleMenuIndex', '/module/menu/index');
Yii::setAlias('@moduleMenuUpdate', '/module/menu/update');
Yii::setAlias('@moduleMenuDelete', '/module/menu/delete');
Yii::setAlias('@moduleMenuCreate', '/module/menu/create');
Yii::setAlias('@moduleMenuSearch', '/module/menu/search');

//错误页面路径
Yii::setAlias('@siteError', '/user/default/error');
Yii::setAlias('@errorAbsoluteUrl',"/modules/site/views/default/error");

// 默认模块命名
Yii::setAlias('@defaultSpotName', '_defaut_module_');
Yii::setAlias('@defaultTemplate', '@RootPath/config/defaultTemplate.php');

// 行为记录
Yii::setAlias('@behaviorActionDelete', '/behavior/record/delete');

//user模块
Yii::setAlias('@userIndexLogin', '/user/index/login');
Yii::setAlias('@userIndexLogout', '/user/index/logout');
Yii::setAlias('@userIndexRegister', '/user/index/register');

//超级站点
Yii::setAlias('@superSpot', 'superSpot');
