<?php

namespace app\modules\rbac\controllers;

use Yii;
use app\modules\rbac\models\PermissionForm;
use app\modules\rbac\controllers\ItemController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Object;
use app\common\base;
use yii\data\Pagination;
use app\common\Common;
use yii\helpers\Json;
use yii\helpers\BaseUrl;
use yii\rbac\Permission;
use yii\helpers\StringHelper;
use app\modules\spot\models\Spot;
/**
 * PermissionController implements the CRUD actions for Permission model.
 */
class PermissionController extends ItemController
{
        
    
    /**
     * Lists all permission models.
     * @return mixed
     * 显示当前选择的权限列表和权限分类列表
     */
    public function actionIndex()
    {
       
        $currentSpot = Yii::$app->request->get('currentSpot');
        $currentCategory = Yii::$app->request->get('currentCategory');//当前权限分类的名称
        $session = Yii::$app->session;
        $session->remove('currentCategory');
        
        if ($currentCategory){
            
            $session->set('currentCategory',$currentCategory);
        }
        $currentCategory = $session->get('currentCategory');
        $currentSpot = $session->get('currentSpot');
        
        $items_new = '';
        $items = '';
        $currentObj = '';
        $categories = $this->manager->getChildren($this->rootPermission);//当前站点的所有分类
        
        //整合数据
        if($currentCategory){
            $currentObj = $this->manager->getPermission($currentCategory);//当前权限分类
            $items = $this->manager->getChildren($currentCategory);//获取当前权限分类下的对应权限列表
             if($items){
                foreach ($items as $v){
             
                    $items_new[$v->data] = $items;
                
                }
             }
           
        }else{//默认显示当前站点所有权限
                       
            foreach ($categories as $v){
                $items[$v->name] = $this->manager->getChildren($v->name) ;
            }
        }
        $currentSpotInfo = '';
        $where = 1;
        //不是超级管理员，就只显示当前站点的权限列表以及分类
        if(!$this->manager->getAssignment(Yii::getAlias('@systemRole'),$this->userInfo->user_id)){
            $where = ['spot' => $this->wxcode];
        }
        $allspot = Spot::find()->select(['spot','spot_name'])->where($where)->all();//所有站点列表
       
        
        
        if($allspot){
            foreach ($allspot as $v){
                
                if($v->spot == $this->wxcode){
                    
                    $currentSpotInfo = array(
                        0 => $v->spot,
                        1 => $v->spot_name
                        
                    );
                }
            }
        }
       
        $model = new PermissionForm();
        $locals = [];
		$locals['model'] = $model;
		$locals['currentSpotInfo'] = $currentSpotInfo;
		$locals['allspot'] = $allspot;
		$locals['categories'] = $categories;
		$locals['items'] = $items_new?$items_new:$items;
        $locals['totalcount'] = count($items);   
        $locals['currentCategory'] = $currentObj?$currentObj->description:'';
       
        return $this->render('index',$locals);
    
    }
    /**
     * 创建站点权限分类
     * 
     */
    public function actionCreate_category(){
        
        $model = new PermissionForm();
        $permission = new \yii\rbac\Permission();
        if ($model->load(Yii::$app->request->post())) {
            
            $category_pm = $model->category;
            if($model->parentName != 'spot' && $model->parentName != 'superspot'){
                $category_pm = $this->permissionPrefix.$category_pm;
            }else{
                $result = Spot::find()->select(['user_id'])->where(['spot' => $category_pm])->all();
                if (!$result){
                    $message = '该站点不存在，请添加站点后，再初始化站点权限哦';
                    Common::showInfo($message);
                   
                }
            }
                    
            $haspermission = $this->manager->getPermission($category_pm);            
            if($haspermission){
                    $message = '该站点权限或权限类型已经存在';
                    Common::showInfo($message);
                 
            };
            $permission->name = $category_pm;
            $permission->type = $model->type;
            $permission->data = $model->parentName;//父类
            $permission->description = $model->description;
           
            $this->manager->add($permission);//添加权限
            $category = new \yii\rbac\Permission();
            //若有当前站点权限根分类，则将新建站点分类关联到其站点根分类，若没。则为初始化当前站点的权限以及其权限根分类,角色
            if($model->parentName != 'spot' && $model->parentName != 'superspot'){             
                $category->name = $model->parentName;                
                $this->manager->addChild($category,$permission);
            }else{
                $configPermission = $model->parentName == 'superspot'?'initSuperPermission':'initPermission';
                $initPermission = Yii::$app->params[$configPermission];
                //站点角色根分类
                $rootWxcodeRole = $this->manager->getRole($category_pm.'_roles');
                if(!$rootWxcodeRole){
                    $rootRoleModel = new \yii\rbac\Role();
                    $rootRoleModel->name = $category_pm.'_roles';
                    $rootRoleModel->description = $model->description;
                    $rootRoleModel->type = 1;
                    $this->manager->add($rootRoleModel);
                    /*若没有初始化站点管理员，则在此新建站点的系统管理员*/
                    $systemRole = $this->manager->getRole($category_pm.'_roles_system');
                    if(!$systemRole){
                        $systemModel = new \yii\rbac\Role();
                        $systemModel->name = $category_pm.'_roles_system';
                        $systemModel->description = '站点管理员';
                        $systemModel->data = $category_pm.'_roles';
                        $systemModel->type = 1;
                        $addsystem = $this->manager->add($systemModel);
                        $this->manager->addChild($rootRoleModel,$systemModel);
                        $this->manager->assign($systemModel, $result[0]['user_id']);
                        
                        
                    }
                }
                
                //初始化站点权限以及站点角色，站点角色名称默认－业务名缩写+'_spot'
                //站点权限根分类名称初始化为－－业务名缩写＋'_permissions'
                //站点下角色根分类名称初始化为－－业务名缩写＋'_roles'
                
                $cols  = $this->manager->getPermission($category_pm.'_permissions');
                if(!$cols){
                    $category->name = $category_pm.'_permissions';//站点默认的权限根分类名称
                    $category->description = $model->description;
                    $category->type = 2;
                    $res = $this->manager->add($category);
                    if($res){
                        //初始化该站点下所有权限分类
                        $permissionModel = new \yii\rbac\Permission();
                        
                        foreach ($initPermission as $k => $v){
                            $permissionModel->name =  $category_pm.'_permissions_'.$k;
                            $permissionModel->description = $v['categoryName'];
                            $permissionModel->data = $category_pm.'_permissions';//所属父类
                            $permissionModel->type = 2;
                            $rows = $this->manager->add($permissionModel);
                            if($rows){
                                $isadd = $this->manager->addChild($category, $permissionModel);//关联该站点的权限根分类
                                if($isadd){
                                    //初始化二级菜单权限
                                    $menuModel = new \yii\rbac\Permission();
                                    if(is_array($v['children'])){
                                        foreach ($v['children'] as $value){
                                            $menuModel->name = $category_pm.$value['name'];
                                            $menuModel->description = $value['description'];
                                            $menuModel->data = $category_pm.'_permissions_'.$k;//所属父类
                                            $menuModel->type = 2;
                                            $ckMenu = $this->manager->add($menuModel);
                                            if($ckMenu){
                                                $this->manager->addChild($permissionModel, $menuModel);//关联父级分类权限
                                                $this->manager->addChild($systemModel, $menuModel);//分配所有二级菜单权限给站点管理员
                                            }
                                        }
                                    }
                                }
                            }
                            
                        }
                    }
                    
                }
                
               
                $spotPrefix = Yii::getAlias("@spotPrefix");
                //添加站点角色，以及关联权限
                $hasRole = $this->manager->getRole($spotPrefix.$category_pm);
                if(!$hasRole){
                    $roleModel = new \yii\rbac\Role();
                    $roleModel->name = $spotPrefix.$category_pm;//站点默认的角色名称
                    $roleModel->type = 1;
                    $roleModel->description = $model->description;
                    $res = $this->manager->add($roleModel);
                    if($res){
                        $this->manager->addChild($roleModel, $permission);
                        $this->manager->assign($roleModel, $result[0]['user_id']);
                    } 
                }               
                Common::showInfo('该站点权限初始化成功');
               
                
            }
             
           
         Common::showInfo("添加权限分类成功");
           
           
        } 
               
        $permission = $this->manager->getPermission($this->rootPermission);
       
       
        if($permission){
             
             $categoryList[$permission->name] = $permission->description; 
        }
        $categoryList['spot'] = '初始化新普通站点';
        $categoryList['superspot'] = '初始化新超级站点';
          return $this->render('create_category', [
                'model' => $model,
                'category' => $categoryList,
                
            ]);
        
    }
    
    
    /**
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        
        $model = new PermissionForm();
        $permission = new \yii\rbac\Permission();
        if ($model->load(Yii::$app->request->post())) {
            $pName = $this->wxcode.'/'.$model->name;
            
            $hasp = $this->manager->getPermission($pName);//判断是否已经存在该权限
            if($hasp){
                $message = '该权限类型已经存在';
                Common::showInfo($message);
                           
            }
            $permission->name = $pName;
            $permission->type = $model->type;
            $permission->data = $model->category;
            $permission->description = $model->description;
            $this->manager->add($permission);
            $spotSystem = $this->manager->getRole($this->rolePrefix.'system');
            if($spotSystem){
                $this->manager->addChild($spotSystem, $permission);//自动将新建权限赋予给站点管理员角色
            }
            if($model->category){
                $category = new \yii\rbac\Permission();
                $category->name = $model->category;
                $this->manager->addChild($category, $permission);
               
            }
            return $this->redirect(['index','currentCategory'=>$model->category]);
        }
            $categories =  $this->manager->getChildren($this->rootPermission);
            return $this->render('create', [
                'model' => $model,
                'categories' => $categories
            ]);
        
    }

    /**
     * Updates an existing Item model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $currentCategory = Yii::$app->request->get('currentCategory');
        $permission = $this->manager->getPermission($id);
        if(!$permission){
            $message = '该权限不存在，请重新选择';
            Common::showInfo($message);
            
        }
        $model = new PermissionForm();
        $model->name = trim(str_replace($this->wxcode.'/', '', $permission->name));
        $model->isNewRecord = false;
        $model->description = $permission->description;
        $model->category = $permission->data;
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $permissionmodel = new Permission();
            $permissionmodel->name = $this->wxcode.'/'.$model->name;
            $permissionmodel->type = $model->type;
            $permissionmodel->data = $model->category;
            $permissionmodel->description = $model->description;
            $this->manager->update($id, $permissionmodel);
            if($model->category != $permission->data){//更新权限关联分类              
                $old_parent = $this->manager->getPermission($permission->data);
                $result = $this->manager->removeChild($old_parent, $permissionmodel);                
                if($result){
                    $parentPermission = $this->manager->getPermission($model->category);
                    $this->manager->addChild($parentPermission, $permissionmodel);
                }
            }

             //var_dump($module.':'.$permission);exit;
            return $this->redirect(['index','currentCategory' => $currentCategory]);
        }
            $category = $this->manager->getChildren($this->wxcode.'_permissions');
            
            return $this->render('update', [
                'model' => $model,
                'category' => $category,
                
       
            ]);
        
    }
    /**
     * 删除一条权限记录
     * (non-PHPdoc)
     * @see \app\modules\rbac\controllers\ItemController::actionDelete()
     */
    public function actionDelete($id){
        
        $obj = $this->manager->getPermission($id);
        if($obj){
            $result = $this->manager->remove($obj);
            $parentPermission = $this->manager->getChildren($obj->data);
            if(!$parentPermission){//若是其父级分类下已经没有其他权限了，则自动清除该权限分类
                $parentObj = $this->manager->getPermission($obj->data);
                $this->manager->remove($parentObj);
            }
        }else{
            Common::showInfo("删除失败--该权限不存在");
        }
        
       Common::showInfo("删除成功");
    }
     
    
  }
    