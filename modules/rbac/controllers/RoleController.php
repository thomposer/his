<?php

namespace app\modules\rbac\controllers;

use Yii;
use app\modules\rbac\models\RoleForm;
use app\modules\rbac\models\search\RoleSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Object;
use app\modules\rbac\models\ItemForm;
use yii\web\Response;
use app\modules\module\models\Title;
/**
 * RoleController implements the CRUD actions for Item model.
 */
class RoleController extends BaseController
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
     * Lists all Item models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize,$this->parentRolePrefix);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Item model.
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
     * Creates a new Item model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RoleForm();
        $model->scenario = 'onlyRole';
        if ($model->load(Yii::$app->request->post())) {
            $model->name = $this->parentRolePrefix.Yii::$app->getSecurity()->generateRandomString(16);
            $model->created_at = time();
            $model->updated_at = time();
            if($model->validate() && $model->save()){
                $this->manager->addChild($this->manager->getRole($this->parentRootRole), $this->manager->getRole($model->name));
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(['index']);
            }
        } 
            return $this->render('create', [
                'model' => $model,
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
        $model = $this->findModel($id);
        $model->scenario = 'onlyRole';
        if ($model->load(Yii::$app->request->post()) ) {

            $model->updated_at = time();
            if($model->validate() && $model->save()){
               Yii::$app->getSession()->setFlash('success','保存成功');
               return $this->redirect(['index']);
            }
        }
            return $this->render('update', [
                'model' => $model,
            ]);
    }
    /**
     * 
     * @param 角色name $id
     * @throws NotFoundHttpException
     * @property 分配角色权限
     */
    public function actionApply($id){
            $model = new ItemForm();
            if($model->load(Yii::$app->request->post())){
                $parent = $this->manager->getRole($id);
                if (!$parent){
                    throw new NotFoundHttpException('你所请求的页面不存在');
                }
                $this->manager->removeChildren($parent);
                if($model->child){
                    foreach ($model->child as $permissionsName){
                        $permissionsObj = $this->manager->getPermission($permissionsName);
                        $this->manager->addChild($parent, $permissionsObj);
                    }
                    $userList = $this->manager->getUserIdsByRole($id);
                    if(!empty($userList)){
                        //清除对应的用户的权限缓存
                        foreach ($userList as $value){
                            $commonRoleMenuCache = Yii::getAlias('@commonRoleMenu') . $value . '_' . $this->parentSpotCode; //普通用户菜单url缓存key
                            $commonAllPermCache = Yii::getAlias('@commonAllPerm') . $value . '_' . $this->parentSpotCode; //普通用户全部权限列表缓存key
                            Yii::$app->cache->delete($commonRoleMenuCache);
                            Yii::$app->cache->delete($commonAllPermCache);
                        }
                    }
                }
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(['index']);
            }
            $permissionsByRole = $this->manager->getPermissionsByRole($id);
            $permissions = $this->manager->getChildren($this->parentRootPermission);
            $data = [];
            $child = [];
            $systemPermission = $this->parentPermissionPrefix.'system';
            $titleList = Title::selectAll();
            /* 整合当前站点的权限分类以及其下对应的权限 */
            if($permissionsByRole){
                foreach ($permissionsByRole as $v){
                    if($v->data != $systemPermission){
                        $data[] = $v->name;
                    }
                }
            }
            foreach ($titleList as $v) {
                $key = $this->parentPermissionPrefix.$v['module_name'];
                if(array_key_exists($key, $permissions)){
                    $tmpData[$key] = $permissions[$key];
                    if(in_array($v['module_name'],['appointment','make_appointment','spot_set','spot'])){
                        $appointment = $this->manager->getChildren($key);
                        if(!empty($appointment)){
                            $childPermissions = [];
                            $sort = [];
                            foreach ($appointment as $k => $value){
                                $grandsonPermissions = [];
                                $grandsonSort = [];
                                if( in_array((substr($k,strlen($this->parentPermissionPrefix))), ['charge-manage', 'charge-config'])){
                                    $grandsonTmpData = $this->manager->getChildren($k);
                                    foreach ($grandsonTmpData as $grandsonKey => $grandsonValue) {
                                        $grandsonSort[$grandsonKey] = $grandsonValue->updatedAt;
                                        $grandsonPermissions[$grandsonKey] = [
                                            'name' => $grandsonValue->name,
                                            'description' => $grandsonValue->description,
                                            'updatedAt' => $grandsonValue->updatedAt,
                                        ];
                                    }
                                }
                                !empty($grandsonPermissions) && array_multisort($grandsonSort,SORT_DESC,$grandsonPermissions);
                                $sort[$k] = $value->updatedAt;
                                $childPermissions[$k] = [
                                   'name' => $value->name,
                                   'description' => $value->description,
                                   'updatedAt' => $value->updatedAt,
                                   'childData' => $grandsonPermissions,
                                ];
                            }
                        }
                        array_multisort($sort,SORT_DESC,$childPermissions);
                        $child[$key] = $childPermissions;
                    }
                    
                }
            }
            $permissions = $tmpData;
            $model->child = array_unique($data);
            unset($permissions[$systemPermission]);
            return $this->render('apply',[
                'model' => $model,
                'permissions' => $permissions,
                'child' => $child
            ]);
    }
    /**
     * Deletes an existing Item model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
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
            if(strpos($id,$this->parentRolePrefix) !== 0 || $id == $this->parentRolePrefix.'system'){
                throw new NotFoundHttpException('你所请求的页面不存在');
            }
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
     * Finds the Item model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Item the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RoleForm::findOne($id)) !== null && strpos($id,$this->parentRolePrefix) === 0) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
