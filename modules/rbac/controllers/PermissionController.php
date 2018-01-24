<?php

namespace app\modules\rbac\controllers;

use Yii;
use yii\web\Response;
use app\modules\rbac\models\PermissionForm;
use app\modules\rbac\models\search\PermissionSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\spot\models\Spot;
use yii\helpers\Url;
use app\modules\rbac\models\search\PermissionCategorySearch;
use yii\helpers\ArrayHelper;
use yii\rbac\Permission;

/**
 * PermissionController implements the CRUD actions for PermissionForm model.
 */
class PermissionController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all PermissionForm models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PermissionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize,$this->parentSpotCode);
        $currentSpotCategory = $searchModel->spotCode?$searchModel->spotCode : $this->parentSpotCode;
        $categories = $this->getCategory($currentSpotCategory);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'categories' => $categories
        ]);
    }

    /**
     * Displays a single PermissionForm model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new PermissionForm model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PermissionForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $pName = $model->name;
            $hasp = $this->manager->getPermission($pName);//判断是否已经存在该权限
            if($hasp){
                $message = '该权限名称已经存在';
                Yii::$app->getSession()->setFlash('error','该记录已经存在');
                $this->redirect(Url::to(['@rbacPermissionCreate']));
                return ;
            }
            $permission = new \yii\rbac\Permission();
            $permission->name = $pName;
            $permission->type = $model->type;
            $permission->data = $model->category;
            $permission->description = $model->description;
            $this->manager->add($permission);
            $spotSystem = $this->manager->getRole($this->parentRolePrefix.'system');
            if($spotSystem){
                $this->manager->addChild($spotSystem, $permission);//自动将新建权限赋予给站点管理员角色
            }
            $category = new \yii\rbac\Permission();
            $category->name = $model->category;
            $this->manager->addChild($category, $permission);
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['index']);
        } else {
            $categories = $this->getCategory($this->parentSpotCode);
            return $this->render('create', [
                'model' => $model,
                'categories' => $categories
            ]);
        }
    }
    
    /**
     * Updates an existing PermissionForm model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $permission = $this->manager->getPermission($id);
        if(!$permission){
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
        $model = new PermissionForm();
        $model->name = $permission->name;
        $model->isNewRecord = false;
        $model->description = $permission->description;
        $model->category = $permission->data;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $permissionmodel = new Permission();
            $permissionmodel->name = $model->name;
            $permissionmodel->type = $model->type;
            $permissionmodel->data = $model->category;
            $permissionmodel->description = $model->description;
            $this->manager->update($id, $permissionmodel);
            if($model->category != $permission->data){//更新权限关联分类
                $old_parent = $this->manager->getPermission($permission->data);
                $result = $this->manager->removeChild($old_parent, $permissionmodel);
                $parentPermission = $this->manager->getPermission($model->category);
                $addStatus = $this->manager->addChild($parentPermission, $permissionmodel);
            }
            Yii::$app->getSession()->setFlash('success','保存成功');
            
            return $this->redirect(['index']);
        } else {
            $categories = $this->getCategory($this->parentSpotCode);
            return $this->render('update', [
                'model' => $model,
                'categories' => $categories
            ]);
        }
    }

    /**
     * Delete an existing PermissionForm model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            $this->findModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }
    }
    /**
     * @return 权限分类列表
     */
    public function actionCategoryIndex(){
        $searchModel = new PermissionCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize,$this->parentSpotCode);
        //当前机构下的二级权限列表
        $permissionSecondList = $this->manager->getChildren($this->rootPermission);
        $spotList = Spot::find()->select(['spot','spot_name'])->where(['parent_spot' => 0,'status' => 1,'type' => 1])->asArray()->all();
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'permissionSecondList' => $permissionSecondList,
            'spotList' => $spotList
        ]);
    }
    /**
     * @return 创建站点权限分类
     *
     */
    public function actionCreateCategory(){
    
        $model = new PermissionForm();
        $permission = new \yii\rbac\Permission();
        if ($model->load(Yii::$app->request->post())) {
    
            $category_pm = $this->parentPermissionPrefix.$model->category;
    
            $haspermission = $this->manager->getPermission($category_pm);
            if($haspermission){
                Yii::$app->getSession()->setFlash('error','该记录已经存在');
                $this->redirect(Url::to(['@rbacPermissionCreateCategory']));
            }
            $permission->name = $category_pm;
            $permission->type = $model->type;
            $permission->data = $this->parentRootPermission;//父类
            $permission->description = $model->description;
             
            $this->manager->add($permission);//添加权限
            $category = new \yii\rbac\Permission();
            //新建站点分类关联到其站点根分类
            $category->name = $this->parentRootPermission;
            $this->manager->addChild($category,$permission);
            Yii::$app->getSession()->setFlash('success','保存成功');
            $this->redirect(Url::to(['@rbacPermissionIndex']));
        }
         
        return $this->render('create-category', [
            'model' => $model,
        ]);
    
    }
    /**
     * 
     * @param 机构代码 $spotCode
     * @return 返回该机构底下的二级和三级权限目录结构
     * 
     */
    protected function getCategory($spotCode){
        $this->parentPermissionPrefix = $spotCode.'_permissions_';
        $this->parentRootPermission = $spotCode.'_permissions';
        $categories =  $this->manager->getChildren($this->parentRootPermission);
        foreach ($categories as $key => $v){
            $categories[$key]->data = '首级分类';
            if($v->name == $this->parentPermissionPrefix.'appointment' || $v->name == $this->parentPermissionPrefix.'make_appointment'){
                $children = $this->manager->getChildren($v->name);
                if(!empty($children)){
                    foreach ($children as $k => $value){
                        $children[$k]->data = '预约';
                    }
                }
            }
        }
        return ArrayHelper::merge($children,$categories);
    }
    /**
     * Finds the PermissionForm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return PermissionForm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PermissionForm::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
