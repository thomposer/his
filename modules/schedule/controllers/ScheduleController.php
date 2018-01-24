<?php

namespace app\modules\schedule\controllers;

use Yii;
use app\modules\schedule\models\Scheduling;
use app\modules\schedule\models\search\SchedulingSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use app\modules\spot_set\models\Schedule;
use yii\helpers\Html;

/**
 * ScheduleController implements the CRUD actions for Scheduling model.
 */
class ScheduleController extends BaseController {


    public function behaviors() {
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
     * Lists all Scheduling models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new SchedulingSearch();
        $schedule = Scheduling::getScheduleList(['id','shift_name'],['status' => 1]);
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'schedule' => $schedule,

        ]);
    }

    public function actionAddScheduling() {
        $date = Yii::$app->request->post('date');
        $worker_id = Yii::$app->request->post('worker_id');
        $schedule_id = Yii::$app->request->post('schedule_id');
        if (!$date || !$worker_id ) {
            $this->result['errorCode'] = 1001;
            return Json::encode($this->result);
        }
        $model = new Scheduling();

        $num=Scheduling::findOne(['spot_id'=>$this->spotId,'user_id' => $worker_id, 'schedule_time' => strtotime(date("Y-m-d", strtotime($date)))]);

        if($num !== null){
            if($num['schedule_id']==0){
                if(empty($schedule_id)){
                    $res = $model->deleteAll('id=:id',array(':id'=>$num['id']));
                    if ($res === 0) {
                        $this->result['errorCode'] = 1002;
                        $this->result['msg'] = '操作失败';
                    }
                }else if($num['schedule_id']!=$schedule_id){
                    $res=$model->updateAll(array('schedule_id'=>$schedule_id),'id=:id',array(':id'=>$num['id']));
                    if ($res === 0) {
                        $this->result['errorCode'] = 1002;
                        $this->result['msg'] = '操作失败';
                    }
                }
            }else{
                if(empty($schedule_id)){
                    $res = $model->deleteAll('id=:id',array(':id'=>$num['id']));
                    if ($res === 0) {
                        $this->result['errorCode'] = 1002;
                        $this->result['msg'] = '操作失败';
                    }
                }else if($num['schedule_id']!=$schedule_id){
                    $res=$model->updateAll(array('schedule_id'=>$schedule_id),'id=:id',array(':id'=>$num['id']));

                    if ($res === 0) {
                        $this->result['errorCode'] = 1002;
                        $this->result['msg'] = '操作失败';
                    }
                }
            }
        }else{
            $res = $model->addScheduling($date, $worker_id, $schedule_id);
            if ($res === false) {
                $this->result['errorCode'] = 1002;
                $this->result['msg'] = '操作失败';
            }
        }

        return Json::encode($this->result);
    }

    public function actionAddAppointment() {
        $date = Yii::$app->request->post('date');
        $worker_id = Yii::$app->request->post('worker_id');
        $schedule_id = Yii::$app->request->post('schedule_id');
        if (!$date || !$worker_id || !$schedule_id) {
            $this->result['errorCode'] = 1001;
            return Json::encode($this->result);
        }
        $model = new Scheduling();
        $res = $model->addScheduling($date, $worker_id, $schedule_id);
        if ($res === false) {
            $this->result['errorCode'] = 1002;
            $this->result['msg'] = '操作失败';
        }
        return Json::encode($this->result);
    }
    /**
     * Displays a single Scheduling model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Scheduling model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Scheduling();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Scheduling model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Scheduling model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();
        Yii::$app->getSession()->setFlash('success', '删除成功');
        return $this->redirect(Yii::$app->request->referrer);
    }

    /**
     * Finds the Scheduling model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Scheduling the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Scheduling::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
