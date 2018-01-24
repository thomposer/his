<?php

namespace app\modules\spot_set\controllers;

use Yii;
use yii\web\Response;
use app\modules\spot_set\models\UserAppointmentConfig;
use app\modules\spot_set\models\search\UserAppointmentConfigSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\common\base\MultiModel;
use app\modules\user\models\UserSpot;
use app\modules\spot_set\models\SpotType;
use app\modules\user\models\User;
use yii\db\Query;
use app\modules\spot_set\models\SecondDepartment;
use yii\db\Exception;

/**
 * UserAppointmentConfigController implements the CRUD actions for UserAppointmentConfig model.
 */
class UserAppointmentConfigController extends BaseController
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
     * Lists all UserAppointmentConfig models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserAppointmentConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);
        
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single UserAppointmentConfig model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Updates an existing UserAppointmentConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $userAppointmentConfigModel = new UserAppointmentConfig();
        $userAppointmentConfigModel->user_id = $id;
        $model = new MultiModel([
            'models' => [
                'userAppointmentConfig' => $userAppointmentConfigModel,
                'userSpot' => $this->findUserSpotModel($id)
            ]
        ]);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                foreach ($model->getModel('userAppointmentConfig')->spot_type_id as $v){
                    $rows[] = [$this->spotId,$id,$v,time(),time()];
                }
                UserAppointmentConfig::deleteAll(['user_id' => $id,'spot_id' => $this->spotId]);
                //批量增加服务预约类型
                Yii::$app->db->createCommand()->batchInsert(UserAppointmentConfig::tableName(),['spot_id','user_id','spot_type_id','create_time','update_time'], $rows)->execute();
                //更新医生诊所的可开放预约状态
                UserSpot::updateAll(['status' => $model->getModel('userSpot')->status,'create_time' => time(),'update_time' => time()],['user_id' => $id,'spot_id' => $this->spotId]);
                $dbTrans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(['index']);
            } catch (Exception $e) {
                $dbTrans->rollBack();
           }
        } else {
            $spotTypeList = SpotType::getSpotType(['status' => 1]);
            $query = new Query();
            $query->from(['a' => User::tableName()]);
            $query->select(['a.id','a.username','b.status','name'=>'group_concat(distinct c.name)','spot_type_id' => 'group_concat(distinct d.spot_type_id)']);
            $query->leftJoin(['b' => UserSpot::tableName()],'{{a}}.id = {{b}}.user_id');
            $query->leftJoin(['c' => SecondDepartment::tableName()],'{{b}}.department_id = {{c}}.id');
            $query->leftJoin(['d' => UserAppointmentConfig::tableName()],'{{a}}.id = {{d}}.user_id');
            $query->where(['a.id' => $id,'b.spot_id' => $this->spotId]);
            $query->groupBy('a.id');
            $userInfo = $query->one();
            return $this->render('update', [
                'model' => $model,
                'spotTypeList' => $spotTypeList,
                'userInfo' => $userInfo
            ]);
        }
    }
    /**
     * 
     * @param int $id 用户id
     * @throws NotFoundHttpException
     * @return \app\modules\user\models\UserSpot
     * @desc 获取用户在当前诊所的开放预约状态
     */
    protected function findUserSpotModel($id){
        if (($model = UserSpot::findOne(['user_id' => $id,'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        } 
    }

    /**
     * Finds the UserAppointmentConfig model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserAppointmentConfig the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserAppointmentConfig::findOne(['user_id' => $id,'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
