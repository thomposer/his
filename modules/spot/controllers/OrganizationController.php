<?php

namespace app\modules\spot\controllers;

use app\common\base\BaseController;
use app\modules\spot\models\CureList;
use app\modules\spot\models\Organization;
use app\modules\spot\models\OrganizationType;
use app\modules\spot\models\search\OrganizationSearch;
use app\modules\spot\models\Spot;
use app\modules\user\models\User;
use Yii;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\rbac\Permission;
use yii\rbac\Role;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * OrganizationController implements the CRUD actions for Organization model.
 */
class OrganizationController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter ::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'httpCache' => [
                'class' => 'yii\filters\HttpCache',
                'only' => ['view'],
                'lastModified' => function ($action, $params) {
                    $query = new \yii\db\Query();
                    $id = \Yii ::$app -> request -> get('id');
                    $result = $query -> from(Spot ::tableName()) -> select('update_time') -> where(['id' => $id]) -> one();

                    return $result['update_time'];
                },
            ],
        ];
    }

    /**
     * Lists all Organization models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrganizationSearch();
        $dataProvider = $searchModel -> search(Yii ::$app -> request -> queryParams, $this -> pageSize);

        return $this -> render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Organization model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this -> render('view', [
            'model' => $this -> findModel($id),
        ]);
    }

    /**
     * Creates a new Organization model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Organization();
        $model -> scenario = 'organization';
        if ($model -> load(Yii ::$app -> request -> post()) && $model -> validate()) {

            $dbTrans = Yii ::$app -> db -> beginTransaction();
            try {
                if ($model -> save()) {
                    $userModel = new User();
                    $userModel -> scenario = 'registerSystem';
                    $userModel -> username = $model -> contact_name;
                    $userModel -> email = $model -> contact_email;
                    $userModel -> iphone = $model -> contact_iphone;
                    $userModel -> spot_id = $model -> id;
                    if ($userModel -> save()) {
                        if ($this -> template($model, $userModel -> id)) {
                            $spotInfo = [
                                'parentSpotName' => $model -> spot_name,
                                'parentSpotCode' => $model -> spot
                            ];
                            $result = $userModel -> sendRegisterMail($userModel, $spotInfo);
                            $this -> insertOrganizationType($model -> id);
                            CureList::insertCureRecord($model->id);
                            Yii ::$app -> getSession() -> setFlash('success', '保存成功');

                        } else {
                            $dbTrans -> rollBack();
                        }
                    } else {
                        $dbTrans -> rollBack();
                    }
                } else {
                    $dbTrans -> rollBack();
                }
                $dbTrans -> commit();

                return $this -> redirect(['index']);
            } catch (Exception $e) {
                var_dump($e->errorInfo);
                $dbTrans -> rollBack();
            }

        } else {

            // 添加一个空白站点模板
//             $defaultList = array (
//                 [
//                     'spot' => Yii::getAlias('@superSpotName'),
//                     'spot_name' => '机构模版'
//                 ],
//             );
            $spotList = Spot ::find()
                -> select(['spot', 'spot_name'])
                -> where(['parent_spot' => 0, 'status' => 1])
                -> orderBy(['id' => SORT_DESC])
                -> asArray()
                -> all();

//             $spotList = ArrayHelper::merge($spotList,$defaultList);
            return $this -> render('create', [
                'model' => $model,
                'templateList' => $spotList
            ]);
        }

    }

    /**
     * @desc 插入两条预约类型
     * @param int $parentSpotId 机构id
     */
    public function insertOrganizationType($parentSpotId)
    {
        $spotTypeList = array([$parentSpotId, '初诊', 30, 1, time(), time()], [$parentSpotId, '复诊', 20, 1, time(), time()]);
        Yii ::$app -> db -> createCommand() -> batchInsert(OrganizationType ::tableName(), ['spot_id', 'name', 'time', 'status', 'create_time', 'update_time'], $spotTypeList) -> execute();
    }



    /**
     * 初始化站点权限和角色
     * 赋予创建该站点的用户-站点管理员角色
     * @throws NotFoundHttpException
     * @return string
     */
    public function template($targetSpot, $user_id)
    {

        $this -> wxcode = $targetSpot -> spot;
        $this -> rolePrefix = $this -> wxcode . '_roles_';
        $this -> permissionPrefix = $this -> wxcode . '_permissions_';
        $this -> rootRole = $this -> wxcode . '_roles';
        $this -> rootPermission = $this -> wxcode . '_permissions';
        // 已经初始化
        $haspermission = $this -> manager -> getPermission($this -> rootPermission);
        if ($haspermission) {
            return false;
        }
        // 使用默认模板初始化,如果有同名的模板，则使用自定义的那个模板
        $isDefaultTemplate = Spot ::find() -> select(['id']) -> where(['spot' => $targetSpot -> template]) -> one() !== null;
        if (!$isDefaultTemplate && ($targetSpot -> template === Yii ::getAlias('@defaultSpotName') || $targetSpot -> template === Yii ::getAlias('@superSpotName'))) {
            return $this -> initDefaultTemplate($targetSpot, $user_id);
        }

        $dbTrans = Yii ::$app -> db -> beginTransaction();
        try {

            $categoryRoleSuffix = '_roles';
            $categoryPermSuffix = '_permissions';

            // 初始化权限分类总目录和角色分类总目录
            $categoryRole = new Role();
            $categoryRole -> name = $this -> rootRole;
            $categoryRole -> description = $targetSpot -> spot_name;
            $this -> manager -> add($categoryRole);

            $categoryPerm = new Permission();
            $categoryPerm -> name = $this -> rootPermission;
            $categoryPerm -> description = $targetSpot -> spot_name;
            $this -> manager -> add($categoryPerm);

            $templateSpotName = $targetSpot -> template;
            $targetSpotName = $targetSpot -> spot;

            // 复制模板的权限
            $templatePerms = $this -> manager -> getChildren($templateSpotName . $categoryPermSuffix);
            foreach ($templatePerms as $perm) {
                $tempPerm = new Permission();
                // 替换前缀为目标站点
                $tempPerm -> name = str_replace($templateSpotName . '_', $targetSpotName . '_', $perm -> name);
                $tempPerm -> description = $perm -> description;
                $tempPerm -> data = $categoryPerm -> name;

                $this -> manager -> add($tempPerm);
                $this -> manager -> addChild($categoryPerm, $tempPerm);

                // 复制该权限下的二级权限，即url资源权限
                $subPerms = $this -> manager -> getChildren($perm -> name);
                foreach ($subPerms as $subPerm) {
                    $subPermName = $targetSpotName . ltrim($subPerm -> name, $templateSpotName);
                    $tempSubPerm = $this -> manager -> getPermission($subPermName);
                    if (!$tempSubPerm) {
                        $tempSubPerm = new Permission();
                        $tempSubPerm -> name = $subPermName;
                        $tempSubPerm -> description = $subPerm -> description;
                        $tempSubPerm -> data = $tempPerm -> name;

                        $this -> manager -> add($tempSubPerm);
                        $threePermissions = $this -> manager -> getChildren($subPerm -> name); //获取其底下的子权限
                        if (!empty($threePermissions)) {//若有，则继续添加对应的权限层级
                            foreach ($threePermissions as $v) {
                                $subPermNameThree = $targetSpotName . ltrim($v -> name, $templateSpotName);
                                $tempSubPermThree = $this -> manager -> getPermission($subPermNameThree);
                                if (!$tempSubPermThree) {

                                    $tempSubPermThree = new Permission();
                                    $tempSubPermThree -> name = $subPermNameThree;
                                    $tempSubPermThree -> description = $v -> description;
                                    $tempSubPermThree -> data = $subPermName;
                                    $this -> manager -> add($tempSubPermThree);
                                    $this -> manager -> addChild($tempSubPerm, $tempSubPermThree);
                                    
                                }
                                
                                $fourChilds = $this->manager->getChildren($v->name);
                                if(!empty($fourChilds)){//若有四级权限
                                    foreach ($fourChilds as $fourV){
                                        $fourPermNameThree = $targetSpotName . ltrim($fourV -> name, $templateSpotName);
                                        $tempFourPermThree = $this->manager->getPermission($fourPermNameThree);
                                        if (!$tempFourPermThree) {
                                            
                                            $tempFourPermThree = new Permission();
                                            $tempFourPermThree -> name = $fourPermNameThree;
                                            $tempFourPermThree -> description = $fourV -> description;
                                            $tempFourPermThree -> data = $subPermNameThree;
                                            $this -> manager -> add($tempFourPermThree);
                                            $this -> manager -> addChild($tempSubPermThree, $tempFourPermThree);
                                            
                                        }
                                        
                                        $FiveChilds = $this->manager->getChildren($fourV->name);
                                        if(!empty($FiveChilds)){//若有五级权限
                                            foreach ($FiveChilds as $fiveV){
                                                $fivePermNameThree = $targetSpotName . ltrim($fiveV -> name, $templateSpotName);
                                                $tempFivePermThree = $this->manager->getPermission($fivePermNameThree);
                                                if (!$tempFivePermThree) {
                                                    
                                                    $tempFivePermThree = new Permission();
                                                    $tempFivePermThree -> name = $fivePermNameThree;
                                                    $tempFivePermThree -> description = $fiveV -> description;
                                                    $tempFivePermThree -> data = $fourPermNameThree;
                                                    $this -> manager -> add($tempFivePermThree);
                                                    $this -> manager -> addChild($tempFourPermThree, $tempFivePermThree);
                                                    
                                                }
                                                
                                                
                                                
                                            }
                                        }
                                        
                                        
                                    }
                                }
                                
                            }
                        }
                    }

                    $this -> manager -> addChild($tempPerm, $tempSubPerm);
                }
            }

            // 复制模板的角色
            $templateRoles = $this -> manager -> getChildren($templateSpotName . $categoryRoleSuffix);
            foreach ($templateRoles as $role) {
                $tempRole = new Role();
                // 替换前缀为目标站点
                $tempRole -> name = str_replace($templateSpotName . '_', $targetSpotName . '_', $role -> name);
                $tempRole -> description = $role -> description;
//                 $tempRole->data = $categoryRole->name;

                $this -> manager -> add($tempRole);
                $this -> manager -> addChild($categoryRole, $tempRole);

                // 复制该角色下的url资源权限
                $subPerms = $this -> manager -> getChildren($role -> name);
                foreach ($subPerms as $subPerm) {

                    $subPermName = $targetSpotName . ltrim($subPerm -> name, $templateSpotName);
                    $tempSubPerm = $this -> manager -> getPermission($subPermName);

                    $this -> manager -> addChild($tempRole, $tempSubPerm);
                }
            }

            // 判断是否有站点管理员，没有则创建一个并赋予申请者
            $systemRoleName = $this -> rolePrefix . 'system';
            $systemRole = $this -> manager -> getRole($systemRoleName);
            if (!$systemRole) {
                $systemRole = new Role();
                $systemRole -> name = $systemRoleName;
                $systemRole -> description = '机构管理员';
                $this -> manager -> add($systemRole);

                // 将所有权限都给站点管理员
                $allPerms = $this -> manager -> getChildren($this -> rootPermission);
                foreach ($allPerms as $perm) {
                    $subPerms = $this -> manager -> getChildren($perm -> name);
                    foreach ($subPerms as $subPerm) {

                        $this -> manager -> addChild($systemRole, $subPerms);
                    }
                }
            }

            // 将站点管理员权限给申请者
            if (!$this -> manager -> getAssignment($systemRoleName, $user_id)) {
                $this -> manager -> assign($systemRole, $user_id);
            }
            // 申请表中添加用户
            $dbTrans -> commit();

            return true;

        } catch (Exception $e) {
            $dbTrans -> rollBack();

            return false;
        }


    }

    /**
     * Updates an existing Organization model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this -> findModel($id);
        $model -> address = $model -> province . '/' . $model -> city . '/' . $model -> area;
        $model -> scenario = 'organization';
        if ($model -> load(Yii ::$app -> request -> post()) && $model -> save()) {
            Yii ::$app -> getSession() -> setFlash('success', '保存成功');

            return $this -> redirect(['index']);
        } else {
            // 添加一个空白站点模板
            $defaultList = array(
                [
                    'spot' => Yii ::getAlias('@superSpotName'),
                    'spot_name' => '机构模版'
                ],

            );

            return $this -> render('update', [
                'model' => $model,
                'templateList' => $defaultList
            ]);
        }
    }

    /**
     * Deletes an existing Organization model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii ::$app -> request;
        if ($request -> isAjax) {

            /*
             *   Process for ajax request
             */
            Yii ::$app -> db -> createCommand() -> update(Spot ::tableName(), ['status' => 2], ['id' => $id]) -> execute();
            Yii ::$app -> response -> format = Response::FORMAT_JSON;

            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this -> redirect(['index']);
        }
    }

    public function initDefaultTemplate($targetSpot, $user_id)
    {
        $rootPath = $targetSpot -> template === Yii ::getAlias('@defaultSpotName') ? Yii ::getAlias('@defaultTemplateUrl') : Yii ::getAlias('@superTemplateUrl');
        $defaultSuperPerms = include($rootPath);
        $dbTrans = Yii ::$app -> db -> beginTransaction();
        try {
            $categoryRoleSuffix = '_roles';
            $categoryPermSuffix = '_permissions';
            $PermSuffix = '_permissions_';
            // 初始化权限分类总目录和角色分类总目录
            $categoryRole = new Role();
            $categoryRole -> name = $targetSpot -> spot . $categoryRoleSuffix;
            $categoryRole -> description = $targetSpot -> spot_name;
            $this -> manager -> add($categoryRole);

            $categoryPerm = new Permission();
            $categoryPerm -> name = $targetSpot -> spot . $categoryPermSuffix;
            $categoryPerm -> description = $targetSpot -> spot_name;
            $this -> manager -> add($categoryPerm);
            //生成默认角色
            $defaultRole = include(Yii ::getAlias('@initDefaultRoleUrl'));
            foreach ($defaultRole as $key => $v) {
                $otherDefaultRole = new Role();
                $otherDefaultRole -> name = $targetSpot -> spot . '_roles_' . $v['name'];
                $otherDefaultRole -> description = $v['description'];
                $this -> manager -> add($otherDefaultRole);
                $this -> manager -> addChild($categoryRole, $otherDefaultRole);
            }
            // 初始化机构管理员用户 站点_roles_用户名称
            $systemRole = new Role();
            $systemRole -> name = $targetSpot -> spot . '_roles_system';
            $systemRole -> description = '机构管理员';
            $this -> manager -> add($systemRole);

            // 添加到角色分类
            $this -> manager -> addChild($categoryRole, $systemRole);
            // 分配站点管理员给申请的用户
            $this -> manager -> assign($systemRole, $user_id);

            // 初始化默认权限-机构管理员权限
            foreach ($defaultSuperPerms as $permName => $perms) {
                $modulePerm = new Permission();
                $modulePerm -> name = $targetSpot -> spot . $PermSuffix . $permName;
                $modulePerm -> description = $perms ['categoryName'];
                $modulePerm -> data = $categoryPerm -> name;

                // 模块权限
                $this -> manager -> add($modulePerm);
                $this -> manager -> addChild($categoryPerm, $modulePerm);

                // 菜单权限
                foreach ($perms ['children'] as $perm) {
                    $temp = new Permission();
                    $temp -> name = $targetSpot -> spot . $perm['name'];
                    $temp -> description = $perm ['description'];
                    $temp -> data = $modulePerm -> name;

                    $this -> manager -> add($temp);
                    // 将权限分配给权限总类以及系统管理员
                    $this -> manager -> addChild($modulePerm, $temp);
                    $this -> manager -> addChild($systemRole, $temp);
                }
            }

//             $defaultPerms = include(Yii::getAlias('@defaultTemplateUrl'));
//             foreach ($defaultPerms as $k => $v){
//                 $moduleDefaultPerm = new Permission();
//                 $moduleDefaultPerm->name = $targetSpot->spot.$PermSuffix . $k;
//                 $moduleDefaultPerm->description = $v['categoryName'];
//                 $moduleDefaultPerm->data = $categoryPerm->name;

//                 // 模块权限
//                 $this->manager->add($moduleDefaultPerm);
//                 $this->manager->addChild($categoryPerm, $moduleDefaultPerm);                
//                 // 菜单权限
//                 foreach ( $v['children'] as $perm ) {
//                     $temp = new Permission();
//                     $temp->name = $targetSpot->spot . $perm['name'];
//                     $temp->description = $perm ['description'];
//                     $temp->data = $moduleDefaultPerm->name;

//                     $this->manager->add($temp);
//                     // 将权限分配给权限总类以及系统管理员
//                     $this->manager->addChild($moduleDefaultPerm, $temp);
//                 }
//             }

            $dbTrans -> commit();

            return true;
        } catch (Exception $e) {
            $dbTrans -> rollBack();
            throw $e;
        }
    }

    /**
     * Finds the Organization model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Organization the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Organization ::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
