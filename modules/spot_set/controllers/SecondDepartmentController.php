<?php

namespace app\modules\spot_set\controllers;

use Yii;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\spot_set\models\search\SecondDepartmentSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\spot_set\models\OnceDepartment;
use yii\web\Response;

/**
 * SecondDepartmentController implements the CRUD actions for SecondDepartment model.
 */
class SecondDepartmentController extends BaseController
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
     * Lists all SecondDepartment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SecondDepartmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);
        $onceDepartmentInfo = OnceDepartment::find()->select(['id','name'])->where(['spot_id' => $this->spotId])->asArray()->all();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'onceDepartmentInfo' => $onceDepartmentInfo
        ]);
    }

    /**
     * Displays a single SecondDepartment model.
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
     * Creates a new SecondDepartment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SecondDepartment();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['index']);
        } else {
            $onceDepartmentInfo = OnceDepartment::find()->select(['id','name'])->where(['spot_id' => $this->spotId])->asArray()->all();
            return $this->render('create', [
                'model' => $model,
                'onceDepartmentInfo' => $onceDepartmentInfo
            ]);
        }
    }

    /**
     * Updates an existing SecondDepartment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->update_time = time();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['index']);
        } else {
            $onceDepartmentInfo = OnceDepartment::find()->select(['id','name'])->where(['spot_id' => $this->spotId])->asArray()->all();
            return $this->render('update', [
                'model' => $model,
                'onceDepartmentInfo' => $onceDepartmentInfo
            ]);
        }
    }

    /**
     * Deletes an existing SecondDepartment model.
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
//             $this->findModel($id)->delete();
            $model = $this->findModel($id);
            $model->status = 3;//已删除;
            $model->save();
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
     * Finds the SecondDepartment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return SecondDepartment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SecondDepartment::find()->where(['id' => $id,'spot_id' => $this->spotId])->andWhere('status != 3')->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
