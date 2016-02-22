<?php

namespace app\modules\rbac\controllers;

use Yii;
use app\modules\rbac\models\RoleForm;
use app\modules\rbac\controllers\ItemController;
use yii\web\NotFoundHttpException;
use app\common\Common;
/**
 * RoleController implements the CRUD actions for Role model.
 */
class RoleController extends ItemController
{
    
    
    /**
     * Lists all Role models.
     * @return mixed
     */
    public function actionIndex()
    {
        /* 获取当前选择站点的全部角色信息 */
        $roles = $this->manager->getChildren($this->rootRole);
        
        $count = count($roles);
        $data = [
              'roles'=>$roles,
              'prefix' => $this->rolePrefix,
              'totalcount'=>$count
            
        ];
        return $this->render('index',$data);
        
    }


    /**
     * Creates a new Item model.
     * 创建角色
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RoleForm();
        $role = new \yii\rbac\Role();
        
        if ($model->load(Yii::$app->request->post())) {
            $roleName = $this->rolePrefix.$model->name;
            if($this->manager->getRole($roleName)){
               Common::showInfo('该角色已经存在');
            }
            $roleRoot = $this->manager->getRole($this->rootRole);
            if(!$roleRoot){
               Common::showInfo('该站点没有角色根分类--'.$this->rootRole.'，请创建后再来把');
            }            
            $role->name = $roleName;
            $role->type = $model->type;
            $role->data = $this->rootRole;
            $role->description = $model->description;
            
            $this->manager->add($role);
            
            //添加角色关联的权限-有则添加
            if(isset($model->child)){
               
                foreach ($model->child as $permissionsName){
                    $permissionsObj = $this->manager->getPermission($permissionsName);
                    $this->manager->addChild($role, $permissionsObj);
                }
            }
            
            $this->manager->addChild($roleRoot, $role);//添加角色关联，区分站点角色
            return $this->redirect(['index']);
        }
            $permissions = $this->manager->getChildren($this->rootPermission);
            
            $data = '';
            /* 整合当前站点的权限分类以及其下对应的权限 */
            if($permissions){
                foreach ($permissions as $v){
                    
                    $data[$v->name] = $this->manager->getChildren($v->name);
                   
                }
            }
          
            return $this->render('create', [
                'model' => $model,
                'permission_parent' => $permissions,//父级权限分类
                'permission_child'=>$data,//父级权限分类下的权限列表
                'type' => '',
            ]);
        
    }
    /**
     * Updates an existing Item model.
     * 更新一个角色信息
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
       
        $roles = $this->manager->getRole($id);
        if(!$roles){
            throw new NotFoundHttpException('你所请求的角色不存在',404);
        }
        $model = new RoleForm();
        
        $model->name = trim(str_replace($this->rolePrefix,'',$roles->name));
        $model->isNewRecord = false;     
        $model->description = $roles->description;
        $rolemodel = new \yii\rbac\Role;
        if ($model->load(Yii::$app->request->post())) {
            
            $rolemodel->name = $this->rolePrefix.$model->name;
            $rolemodel->type = $model->type;
            $rolemodel->data = $this->rootRole;
            $rolemodel->description = $model->description;
            $this->manager->update($id, $rolemodel);
            $permission = '';
            if($model->child != null){//若选择权限不为空
                /*清空原角色的所有权限，并重新赋予新的权限 */
                $this->manager->removeChildren($rolemodel);
             
                foreach ($model->child as $permissionName){  
                                
                    $permissionObj = $this->manager->getPermission($permissionName);
                    
                    $this->manager->addChild($rolemodel, $permissionObj);
                
                }

            }   
            return $this->redirect(['index']);
        }
                     
            $selectedpermission = $this->manager->getPermissionsByRole($id);//获取当前角色下的所有权限
            if($selectedpermission != NULL){
                foreach ($selectedpermission as $p){
                    $model->child[] = $p->name;
                }   
            }
            $permissions = $this->manager->getChildren($this->rootPermission);//获取当前站点
            
            $data = '';
            $userId =  $this->userInfo->user_id;
            if($permissions){
                foreach ($permissions as $key => $v){
                    $data[$v->name] = $this->manager->getChildren($v->name);
                    
                }
            }
        $locals['permission_parent'] = $permissions;
        $locals['permission_child'] = $data;      
        $locals['model'] = $model;
        
        return $this->render('update',$locals);
        
    }
    
    public function actionView($id){
        
        $roles = $this->manager->getRole($id);
        $model = new RoleForm();
        $model->name = $roles->name;
        $model->description = $roles->description;
        $role_permission = $this->manager->getPermissionsByRole($id);
        return $this->render('view',[
            'model' => $model,
            'rolepermission' => $role_permission
        ]);
    }
    /**
     * 删除一个角色
     * (non-PHPdoc)
     * @see \app\modules\rbac\controllers\ItemController::actionDelete()
     */
    public function actionDelete($id){
        $obj = $this->manager->getRole($id);
        if($obj){
            $result = $this->manager->remove($obj);
        }else{
            Common::showInfo("删除失败--该角色不存在");
        }
        Common::showInfo('删除成功');
    }
    
}
