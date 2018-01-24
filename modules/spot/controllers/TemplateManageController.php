<?php

namespace app\modules\spot\controllers;

use Yii;
use yii\web\Response;
use app\modules\spot\models\NursingRecordTemplate;
use app\modules\spot\models\search\NursingRecordTemplateSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\spot\models\search\CaseTemplateSearch;
use app\modules\spot\models\CaseTemplate;
use app\modules\spot\models\ChildCareTemplate;
use app\modules\spot\models\search\ChildCareTemplateSearch;

/**
 * NursingRecordTemplateController implements the CRUD actions for NursingRecordTemplate model.
 */
class TemplateManageController extends BaseController
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
     * ================================病例模板管理=========================
     */

    /**
     * Lists all CaseTemplate models.
     * @return mixed
     */
    public function actionCaseIndex() {

        $searchModel = new CaseTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);


        return $this->render('case-template/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CaseTemplate model.
     * @param string $id
     * @return mixed
     */
    public function actionCaseView($id) {
        return $this->render('case-template/view', [
                    'model' => $this->findCaseModel($id),
        ]);
    }

    /**
     * Creates a new CaseTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCaseCreate() {
        $model = new CaseTemplate();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->saveType) {
                //医生门诊保存信息
                return $this->saveCase($model);
            } else {
                if ($model->validate()) {
                    $model->save();
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(['case-index']);
                } else {
                    return $this->render('case-template/create', [
                                'model' => $model,
                    ]);
                }
            }
        } else {
            return $this->render('case-template/create', [
                        'model' => $model,
            ]);
        }
    }

    protected function saveCaseCase($model) {
        if ($model->saveType == 1 && $model->caseId) { //更新
            $model = $this->findCaseModel($model->caseId);
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
    public function actionCaseUpdate($id) {
        $model = $this->findCaseModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['case-index']);
        } else {
            return $this->render('case-template/update', [
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
    public function actionCaseDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {

            /*
             *   Process for ajax request
             */
            $this->findCaseModel($id)->delete();

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
     * ================================儿保模板管理=========================
     */
    public function actionChildIndex() {

        $searchModel = new ChildCareTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);


        return $this->render('child-template/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CaseTemplate model.
     * @param string $id
     * @return mixed
     */
    public function actionChildView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CaseTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionChildCreate() {
        $model = new ChildCareTemplate();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->save();
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['child-index']);
        } else {
            return $this->render('child-template/create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CaseTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionChildUpdate($id) {
        $model = $this->findChildCareModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['child-index']);
        } else {
            return $this->render('child-template/update', [
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
    public function actionChildDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $this->findChildCareModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload'=>'#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['child-index']);
        }
    }

    /**
     * ================================护理模板管理=========================
     */

    /**
     * Lists all NursingRecordTemplate models.
     * @return mixed
     */
    public function actionNursingIndex()
    {
        $searchModel = new NursingRecordTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);

        return $this->render('nursing-template/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single NursingRecordTemplate model.
     * @param string $id
     * @return mixed
     */
    public function actionNursingView($id)
    {
        return $this->render('nursing-template/view', [
            'model' => $this->findNursingModel($id),
        ]);
    }

    /**
     * Creates a new NursingRecordTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionNursingCreate()
    {
        $model = new NursingRecordTemplate();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['nursing-index']);
        } else {
            return $this->render('nursing-template/create', [
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
    public function actionNursingUpdate($id)
    {
        $model = $this->findNursingModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['nursing-index']);
        } else {
            return $this->render('nursing-template/update', [
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
    public function actionNursingDelete($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            $this->findNursingModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['nursing-index']);
        }
    }

    /**
     * ==========================================================================================
     */

    /**
     * Lists all NursingRecordTemplate models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new NursingRecordTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

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
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new NursingRecordTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new NursingRecordTemplate();
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
     * Updates an existing NursingRecordTemplate model.
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
     * Delete an existing NursingRecordTemplate model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
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
     * Finds the NursingRecordTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return NursingRecordTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = NursingRecordTemplate::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * 
     * @param type $id
     * @return type 儿保Model
     * @throws NotFoundHttpException
     */
    protected function findChildCareModel($id) {
        if (($model = ChildCareTemplate::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     *
     * @param type $id
     * @return type 儿保Model
     * @throws NotFoundHttpException
     */
    protected function findCaseModel($id) {
        if (($model = CaseTemplate::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * Finds the NursingRecordTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return NursingRecordTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findNursingModel($id)
    {
        if (($model = NursingRecordTemplate::findOne(['id' => $id,'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
