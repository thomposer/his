<?php

namespace app\modules\spot_set\controllers;

use Yii;
use app\modules\spot_set\models\Schedule;
use app\modules\spot_set\models\search\ScheduleSearch;
use app\common\base\BaseController;
use yii\helpers\Html;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * ScheduleController implements the CRUD actions for Schedule model.
 */
class ScheduleController extends BaseController
{

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
     * Lists all Schedule models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ScheduleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Schedule model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Schedule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Schedule();
        $model->spot_id = $this->spotId;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $post_data = Yii::$app->request->post()['Schedule'];
            $shift_time = [];
            foreach ($post_data['shift_timef'] as $key => $item) {
                if ($item) {
                    if ($post_data['shift_timet'][$key]) {
                        $shift_time[] = $item . '~' . $post_data['shift_timet'][$key];
                    }
                }
            }
            !empty($shift_time) && $model->shift_time = implode('/', $shift_time);
            $res = $model->save();
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            $model->load(Yii::$app->request->post());
            if (isset(Yii::$app->request->post()['Schedule'])) {
                $post_data = Yii::$app->request->post()['Schedule'];
                $shift_time = $this->setShiftTime($post_data);
                !empty($shift_time) && $model->shift_time = implode('/', $shift_time);
            }
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Schedule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $post_data = Yii::$app->request->post()['Schedule'];
            $shift_time = $this->setShiftTime($post_data);
            !empty($shift_time) && $model->shift_time = implode('/', $shift_time);
            $model->save();
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {

            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    protected function setShiftTime($post_data) {
        $shift_time = [];
        foreach ($post_data['shift_timef'] as $key => $item) {
            if ($item) {
                if ($post_data['shift_timet'][$key]) {
                    $shift_time[] = $item . '~' . $post_data['shift_timet'][$key];
                }
            }
        }
        return $shift_time;
    }

    /**
     * Deletes an existing Schedule model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {

            /*
             *   Process for ajax request
             */
            $this->findModel($id)->delete();

            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Schedule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Schedule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Schedule::findOne(['spot_id' => $this->spotId, 'id' => $id])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在.');
        }
    }

}
