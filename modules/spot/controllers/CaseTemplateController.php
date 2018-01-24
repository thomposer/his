<?php

namespace app\modules\spot\controllers;

use Yii;
use app\modules\spot\models\CaseTemplate;
use app\modules\spot\models\search\CaseTemplateSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\common\base\BaseController;
use yii\web\Response;

/**
 * CaseTemplateController implements the CRUD actions for CaseTemplate model.
 */
class CaseTemplateController extends BaseController
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
     * Lists all CaseTemplate models.
     * @return mixed
     */
    public function actionIndex() {

        $searchModel = new CaseTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);


        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CaseTemplate model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CaseTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CaseTemplate();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->saveType) {
                //医生门诊保存信息
                return $this->saveCase($model);
            } else {
                if ($model->validate()) {
                    $model->save();
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(['index']);
                } else {
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    protected function saveCase($model) {
        if ($model->saveType == 1 && $model->caseId) { //更新
            $model = $this->findModel($model->caseId);
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                $this->result['errorCode'] = 0;
                $this->result['msg'] = '保存成功';
            } else {
                $this->result['errorCode'] = 1001;
                $errors = $model->getErrors('name');
                $this->result['msg'] = $errors ? $errors[0] : '操作失败';
            }
        } else {
            $this->result['errorCode'] = 1002;
            $errors = $model->getErrors('name');
            $this->result['msg'] = $errors ? $errors[0] : '操作失败';
        }
        return \yii\helpers\Json::encode($this->result);
    }

    /**
     * Updates an existing CaseTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CaseTemplate model.
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
     * Finds the CaseTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CaseTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = CaseTemplate::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

}
