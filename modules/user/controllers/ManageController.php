<?php

namespace app\modules\user\controllers;

use Yii;
use app\modules\user\models\User;
use app\modules\user\models\search\UserSearch;
use app\common\base\BaseController;
use yii\helpers\Json;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\spot\models\Spot;
use app\modules\user\models\UserSpot;
use yii\helpers\Url;
use app\modules\rbac\models\AssignmentForm;
use yii\db\Query;
use yii\web\Response;
use yii\web\NotAcceptableHttpException;
use app\modules\spot_set\models\OnceDepartment;
use app\modules\spot_set\models\SecondDepartment;
use yii\db\Exception;
use yii\filters\AccessControl;
/**
 * ManageController implements the CRUD actions for User model.
 */
class ManageController extends BaseController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    // 允许认证用户
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    // 默认禁止其他用户
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);
        $spotNameList = User::getSpotList($dataProvider->keys);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'spotNameList' => $spotNameList,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $spotNameList = User::getSpotList($id);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'spotNameList' => $spotNameList
        ]);
    }
    public function actionInfo(){
        $spotNameList = User::getSpotList($this->userInfo->id);
        $info = User::find()->select(['id','username','iphone','email','sex','card','head_img','birthday','occupation','occupation_type','introduce','position_title'])->where(['id' => $this->userInfo->id])->one();
        return $this->render('view',[
            'model' => $info,
            'spotNameList' => $spotNameList
        ]);
    }
    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new User();
        $model->scenario = 'register';
        $model->spot_id = $this->parentSpotId;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($model->role) {
                foreach ($model->role as $v) {
                    $roleItem = $this->manager->getRole($v);
                    $hasRecord = $this->manager->getAssignment($v, $model->id);
                    if (!$hasRecord) {
                        $this->manager->assign($roleItem, $model->id);
                    }
                }
            }
            foreach ($model->clinic_id as $key => $v) {
                $userSpotModel = new UserSpot();
                $userSpotModel->user_id = $model->id;
                $userSpotModel->parent_spot_id = $this->parentSpotId;
                $userSpotModel->spot_id = $v;
                $userSpotModel->department_id = $model->department[$key];
                $userSpotModel->status = 2;
                $userSpotModel->save();
            }
            $result = $model->sendRegisterMail($model);
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        }
        $clinic_id[] = array();
        $department[] = array();
        if(isset(Yii::$app->request->post()['User'])){
            $clinic=Yii::$app->request->post()['User'];
            $clinic_id=$clinic['clinic_id'];
            $department=$clinic['department'];
        }
        $roleInfo = $this->manager->getChildren($this->parentRootRole);
        $spotInfo = Spot::find()->select(['id', 'spot_name'])->where(['parent_spot' => $this->parentSpotId, 'status' => 1])->asArray()->all();
        return $this->render('create', [
                    'model' => $model,
                    'spotInfo' => $spotInfo,
                    'roleInfo' => $roleInfo,
                    'clinicInfo' => $clinic_id,
                    'department' => $department
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scenario = 'register';
        if($model->birthday == '0000-00-00'){
            $model->birthday = '';
        }
        $roleInfo = $this->manager->getChildren($this->parentRootRole);
        $userSpotInfo = $model->getUserSpot()->asArray()->all(); //关联诊所
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $permsCheck = array();
                if($model->role){
                    foreach ($model->role as $v){
                        $permsCheck[$v] = true;
                    }
                }
                    foreach ($roleInfo as $perm) {
                        $permName = $perm->name;
                    
                        $role = $this->manager->getRole($permName);
                        // 添加选中的角色
                        if (isset($permsCheck[$permName])) {
                            // 没有则添加
                            if (! $this->manager->getAssignment($permName, $model->id)) {
                                $this->manager->assign($role, $model->id);
                            }
                        } else {
                            // 删除未选中的角色
                            $this->manager->revoke($role, $model->id);
                        }
                    }
                
                $command = Yii::$app->db->createCommand('delete from '.UserSpot::tableName().' where user_id = :id and parent_spot_id = :parent_spot_id');
    		    $command->bindValue(':id',$id);
    		    $command->bindValue(':parent_spot_id',$this->parentSpotId);
                $queryResult = $command->query();
                if($queryResult){
                    $rows = [];
                    foreach ($model->clinic_id as $key => $v){
                        $rows[$key] = [$model->id, $this->parentSpotId, $v, $model->department[$key]?$model->department[$key]:0,1];
                        foreach ($userSpotInfo as $value){
                            if ($value['spot_id'] == $v) {
                                $rows[$key] = [$model->id, $this->parentSpotId, $v, $model->department[$key]?$model->department[$key]:0,$value['status']];
                                break;
                            }
                        }
                    }
                    Yii::$app->db->createCommand()->batchInsert(UserSpot::tableName(),['user_id','parent_spot_id','spot_id','department_id','status'],$rows)->execute();
                }
                if(!in_array($model->default_spot, $model->clinic_id)){
                    $model->default_spot = 0;
                }
                $model->save();
                Yii::$app->cache->delete(Yii::getAlias('@spotList').$this->parentSpotId.'_'.$id);
                (new Spot())->getCacheSpotList($id);
                $dbTrans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(['index']);
            }catch (Exception $e) {
                $dbTrans->rollBack();
                Yii::$app->getSession()->setFlash('success', '保存失败');
                return $this->redirect(['index']);
            }
            
        }else{
            
            $spotInfo = Spot::find()->select(['id','spot_name'])->where(['parent_spot' => $this->parentSpotId,'status' => 1])->indexBy('id')->asArray()->all();
            if (isset(Yii::$app->request->post()['User'])) {
                $clinic = Yii::$app->request->post()['User'];
                $clinic_id = $clinic['clinic_id'];
                $department = $clinic['department'];
            } elseif ($userSpotInfo) {
                foreach ($userSpotInfo as $v) {
                    if(array_key_exists($v['spot_id'], $spotInfo)){
                        $clinic_id[] = $v['spot_id'];
                        $department[] = $v['department_id'];
                    }
                }
            } else {
                $clinic_id[] = array();
                $department[] = array();
            }
            $userRoleQuery = AssignmentForm::find()->select(['item_name'])->where(['user_id' => $model->id]);
            $userRoleQuery->andFilterWhere(['like','item_name',$this->parentRolePrefix.'%',false]);
            $userRoleInfo = $userRoleQuery->asArray()->all();
            if($userRoleInfo){
                foreach ($userRoleInfo as $v){
                    $model->role[] = $v['item_name'];
                }
            }
            if(empty($clinic_id)){
                $clinic_id[] = array();
                $department[] = array();
            }
            return $this->render('update', [
                'model' => $model,
                'spotInfo' => $spotInfo,
                'roleInfo' => $roleInfo,
                'clinicInfo' => $clinic_id,
                'department' => $department
            ]);
        }
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            
            /*
             *   Process for ajax request
             */
            if($this->userInfo->id == $id){
                throw new NotAcceptableHttpException('你所请求的页面不存在');
            }
            $model = $this->findModel($id);
            $model->scenario = 'delete';
            $model->status = 3;//已删除
            $model->save();
//             AssignmentForm::deleteAll('user_id = :user_id and item_name like :item_name',[':user_id' => $userData->id,':item_name' => $this->parentRolePrefix.'%']);
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
     * @property 修改密码
     * @return string
     */
    public function actionEditPassword(){
        $model = User::findIdentity($this->userInfo->id);
        $model->scenario = 'editPassword';
        if ($model->load(Yii::$app->request->post())) {
            $model->update_time = time();   
            if($model->validate()){
                $model->generatePasswordHash();
                $model->save();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(['info']);
            }
        }
        return $this->render('edit-password', [
            'model' => $model,
        ]);   
    }
    /**
     * 重置密码api
     */
    public function actionReset($id){
        $query = new \yii\db\Query();
        $query->from(['a' => User::tableName()]);
        $query->select(['id','email','username','password_reset_token']);
        $query->where(['id' => $id]);
        $result = $query->one();
        if(!$result){
            throw new NotFoundHttpException('你所请求的页面不存在',404);
        }
        $this->sendResetEmail($result);
    }
    
    //发送重置密码邮件
    public function sendResetEmail($data){
        
        $parentSpotName = $this->parentSpotName;
        $parentSpotCode = $this->parentSpotCode;
        $model = User::find()->select(['id','expire_time'])->where(['id' => $data['id']])->one();
        $model->scenario = 'resetSave';
        $model->expire_time = time()+86400;       
        if($model->save()){
//             $spotName = $_COOKIE['parentSpotName'];
            $mail= Yii::$app->mailer->compose(Yii::getAlias('@resetEmail'),['data' => $data,'parentSpotName' => $parentSpotName,'parentSpotCode' => $parentSpotCode]);
//             $mail->setFrom(Yii::getAlias('@emailName'));
            $mail->setTo($data['email']);
            $mail->setSubject("【重置密码】".$parentSpotName);
            $mail->setReplyTo(['360766414@qq.com' => '张震宇']);
            //邮件发送成功后，重置expire_time
            if($mail->queue()){
                Yii::$app->getSession()->setFlash('success','发送邮件成功');
    
            }else {
   
                Yii::$app->getSession()->setFlash('error','发送邮件失败');
            }
    
        }else{

            Yii::$app->getSession()->setFlash('error','发送邮件失败');
        }
        return $this->redirect(Url::to(['@userManageIndex']));
    }
       
    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        $model = User::findOne(['id' => $id,'spot_id' => $this->parentSpotId]);
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    public function actionDepartmentSelect(){//没用到,无需修改
        $postData = Yii::$app->request->post();
        $clinicId = $postData['clinic_id'];
        $query = new Query();
        $query->from(['a' => SecondDepartment::tableName()]);
        $query->select(['a.id','a.name','a.parent_id','b.name as onceName']);
        $query->leftJoin(['b' => OnceDepartment::tableName()],'{{a}}.parent_id = {{b}}.id');
        $query->where(['a.spot_id' => $clinicId,'a.status'=>1]);
        $departmentInfo = $query->all();
        $clinicRoom = [];
         foreach($departmentInfo as $key=> $val){
            $clinicRoom[$departmentInfo[$key]['onceName']][] = $val;
        }
        $result['data'] = $clinicRoom;
        return Json::encode($result);

    }

}
