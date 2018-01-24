<?php

namespace app\modules\spot_set\controllers;

use app\modules\spot\models\Spot;
use Yii;
use app\modules\spot_set\models\Room;
use app\modules\spot_set\models\search\RoomSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\modules\spot\models\SpotConfig;
use app\modules\spot_set\models\UserAppointmentConfig;
use app\common\base\MultiModel;
use app\modules\user\models\UserSpot;
use yii\db\Exception;
use app\modules\spot_set\models\SpotType;
use yii\db\Query;
use app\modules\user\models\User;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\spot_set\models\search\UserAppointmentConfigSearch;
use app\modules\spot_set\models\UserPriceConfig;

/**
 * RoomController implements the CRUD actions for Room model.
 */
class AppointmentTypeController extends BaseController
{
    use CustomAppointmentTrait;
    
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'custom-appointment-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @param $id
     * @return 其它设置（预约设置）
     * @throws NotFoundHttpException
     */
//    public function actionIndex() {
//        if (( $model = Spot::findOne(['id' => $this->spotId])) !== null) {
//            if(Yii::$app->request->isAjax) {
//                if ($model->load(Yii::$app->request->post())) {
//                    //$appointment_type=implode(',',Yii::$app->request->post()['Spot']['appointment_type']);
//                    $appointment_type = Yii::$app->request->post()['Spot']['appointment_type'];
//                    Yii::$app->db->createCommand()->update(Spot::tableName(), ['appointment_type' => $appointment_type], ['id' => $this->spotId])->execute();
//                    Yii::$app->getSession()->setFlash('success', '保存成功');
//                    return $this->redirect(['index']);
//                }
//            }
//
//            $model->appointment_type = explode(',',$model->appointment_type);
//            return $this->render('index',[
//                'model' => $model,
//            ]);
//        } else {
//            throw new NotFoundHttpException('你所请求的页面不存在');
//        }
//    }

    public function actionIndex() {
        $model = SpotConfig::findOne(['spot_id' => $this->spotId]);
        if (!$model) {
            $model = new SpotConfig();
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('appointment', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Lists all UserAppointmentConfig models.
     * @return mixed
     */
    public function actionUserAppointmentIndex() {
        $searchModel = new UserAppointmentConfigSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        $query = new Query();
        $query->from(['a' => UserAppointmentConfig::tableName()]);
        $query->select(['a.spot_type_id', 'a.user_id', 'type' => "group_concat(b.type,' ',IFNULL(a.price,'') ORDER BY b.id ASC SEPARATOR ' | ')"]);
        $query->leftJoin(['b' => SpotType::tableName()], '{{a}}.spot_type_id = {{b}}.id');
        $query->where(['a.spot_id' => $this->spotId,'b.status' => 1]);
        $query->orderBy(['a.id' => SORT_ASC]);
        $query->groupBy('a.user_id');
        $query->indexBy('user_id');
        $typeInfo = $query->all();
        
        $query = new Query();
        $query->from(['a' => UserPriceConfig::tableName()]);
        $query->select(['a.price', 'a.user_id']);
        $query->where(['a.spot_id' => $this->spotId]);
        $query->groupBy('a.user_id');
        $query->indexBy('user_id');
        $priceInfo = $query->all();
        foreach ($dataProvider->keys as $value) {
            if(isset($typeInfo[$value])){
                $typeInfo[$value]['type'] .= ' | 方便门诊 ' . $priceInfo[$value]['price'];
            }else{
                $typeInfo[$value]['type'] = '方便门诊 ' . $priceInfo[$value]['price'];
            }
        }
        return $this->render('/user-appointment-config/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'typeInfo' => $typeInfo,
        ]);
    }

    /**
     * Updates an existing UserAppointmentConfig model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $userAppointmentConfigModel = new UserAppointmentConfig();
        $userAppointmentConfigModel->user_id = $id;
        $model = new MultiModel([
            'models' => [
                'userAppointmentConfig' => $userAppointmentConfigModel,
                'userSpot' => $this->findUserSpotModel($id),
                'userPriceConfig' => $this->findUserPriceConfigModel($id)
            ]
        ]);
        if (Yii::$app->request->post()) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $userAppointmentConfigModel = $model->getModel('userAppointmentConfig');
                    foreach ($userAppointmentConfigModel->spot_type_id as $v) {
                        $rows[] = [$this->spotId, $id, $v, $userAppointmentConfigModel->price[$v],time(), time()];
                    }
                    UserAppointmentConfig::deleteAll(['user_id' => $id, 'spot_id' => $this->spotId]);
                    //批量增加服务预约类型
                    Yii::$app->db->createCommand()->batchInsert(UserAppointmentConfig::tableName(), ['spot_id', 'user_id', 'spot_type_id', 'price', 'create_time', 'update_time'], $rows)->execute();
                    //更新医生诊所的可开放预约状态
                    UserSpot::updateAll(['status' => $model->getModel('userSpot')->status, 'create_time' => time(), 'update_time' => time()], ['user_id' => $id, 'spot_id' => $this->spotId]);
                    $model->getModel('userPriceConfig')->save();
                    $dbTrans->commit();
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(['user-appointment-index']);
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    $this->result['errorCode'] = $e->errorInfo[1];
                    $this->result['msg'] = $e->errorInfo[2];
                    return $this->result;
                }
            } else {
                if (!empty($model->getModel('userSpot')->errors)) {
                    $errorMsg = $model->getModel('userSpot')->errors[0][0];
                } else if (!empty($model->getModel('userAppointmentConfig')->errors)) {
                    $errorMsgArr = array_values($model->getModel('userAppointmentConfig')->errors);
                    $errorMsg = $errorMsgArr[0][0];
                } else if(!empty($model->getModel('userPriceConfig')->errors)){
                    $errorMsgArr = array_values($model->getModel('userPriceConfig')->errors);
                    $errorMsg = $errorMsgArr[0][0];
                }
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = $errorMsg;
                return $this->result;
            }
        } else {
            $spotTypeList = SpotType::getSpotType(['status' => 1]);
            $query = new Query();
            $query->from(['a' => User::tableName()]);
            $query->select(['a.id', 'a.username', 'b.status', 'name' => 'group_concat(distinct c.name)']);
            $query->leftJoin(['b' => UserSpot::tableName()], '{{a}}.id = {{b}}.user_id');
            $query->leftJoin(['c' => SecondDepartment::tableName()], '{{b}}.department_id = {{c}}.id');
            $query->where(['a.id' => $id, 'b.spot_id' => $this->spotId]);
            $query->groupBy('a.id');
            $userInfo = $query->one();
            $userTypeList = UserAppointmentConfig::getTypePriceList(['user_id' => $id, 'spot_id' => $this->spotId]);
            return $this->render('/user-appointment-config/update', [
                        'model' => $model,
                        'spotTypeList' => $spotTypeList,
                        'userTypeList' => $userTypeList,
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
    protected function findUserSpotModel($id) {
        if (($model = UserSpot::findOne(['user_id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
    
    protected function findUserPriceConfigModel($id) {
        if (($model = UserPriceConfig::findOne(['user_id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            $model = new UserPriceConfig();
            $model->user_id = $id;
            $model->spot_id = $this->spotId;
            return $model;
        }
    }

}
