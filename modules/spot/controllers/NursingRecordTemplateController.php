<?php

namespace app\modules\spot\controllers;

use Yii;
use yii\web\Response;
use app\modules\spot\models\NursingRecordTemplate;
use app\modules\spot\models\search\NursingRecordTemplateSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * NursingRecordTemplateController implements the CRUD actions for NursingRecordTemplate model.
 */
class NursingRecordTemplateController extends BaseController
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
     * Lists all NursingRecordTemplate models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new NursingRecordTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NursingRecordTemplate model.
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
     * Creates a new NursingRecordTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new NursingRecordTemplate();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing NursingRecordTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Delete an existing NursingRecordTemplate model.
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
     * Finds the NursingRecordTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return NursingRecordTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NursingRecordTemplate::findOne(['id' => $id,'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
