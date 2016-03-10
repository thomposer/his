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
        if(!$this->manager->checkAccess($this->userInfo->user_id,Yii::getAlias('@systemPermission'))){
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
            
            $category_pm = $this->permissionPrefix.$model->category;
            
            $haspermission = $this->manager->getPermission($category_pm);            
            if($haspermission){
                    $message = '该权限分类已经存在';
                    Common::showInfo($message);
            }
            $permission->name = $category_pm;
            $permission->type = $model->type;
            $permission->data = $this->rootPermission;//父类
            $permission->description = $model->description;
           
            $this->manager->add($permission);//添加权限
            $category = new \yii\rbac\Permission();
            //新建站点分类关联到其站点根分类
            $category->name = $this->rootPermission;                
            $this->manager->addChild($category,$permission);
            Common::showInfo("添加权限分类成功");
           
           
        } 
               
          return $this->render('create_category', [
                'model' => $model,
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
            $pName = $this->wxcode.$model->name;
            
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
            $category = new \yii\rbac\Permission();
            $category->name = $model->category;
            $this->manager->addChild($category, $permission);
               
            
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
        $model->name = ltrim($permission->name,$this->wxcode);
        $model->isNewRecord = false;
        $model->description = $permission->description;
        $model->category = $permission->data;
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $permissionmodel = new Permission();
            $permissionmodel->name = $this->wxcode.$model->name;
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

            return $this->redirect(['index','currentCategory' => $currentCategory]);
        }
            $category = $this->manager->getChildren($this->rootPermission);
            
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
    