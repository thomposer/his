<?php

namespace app\modules\outpatient\controllers;

use Yii;
use yii\web\Response;
use app\modules\outpatient\models\DentalFirstTemplate;
use app\modules\outpatient\models\search\DentalFirstTemplateSearch;
use yii\web\NotFoundHttpException;
use yii\db\ActiveQuery;
use app\modules\user\models\User;

/**
 * DentalController implements the CRUD actions for DentalFirstTemplate model.
 */

trait DentalFirstTrait{

    /**
     * Lists all DentalFirstTemplate models.
     * @return mixed
     */
    public function actionDentalfirstIndex()
    {
        $searchModel = new DentalFirstTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);

        return $this->render('/dental-first/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DentalFirstTemplate model.
     * @param integer $id
     * @return mixed
     */
    public function actionDentalfirstView($id)
    {
        return $this->render('/dental-first/view', [
            'model' => $this->findDentalfirstModel($id),
        ]);
    }

    /**
     * Creates a new DentalFirstTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionDentalfirstCreate()
    {
        $model = new DentalFirstTemplate();
        $model->type = 2;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['dentalfirst-index']);
        } else {
            return $this->render('/dental-first/create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing DentalFirstTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDentalfirstUpdate($id)
    {
        $model = $this->findDentalfirstModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['dentalfirst-index']);
        } else {
            return $this->render('/dental-first/update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Delete an existing DentalFirstTemplate model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    
    public function actionDentalfirstDelete($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            $this->findDentalfirstModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['/dental-first/index']);
        }
    }

    /**
     * Finds the DentalFirstTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DentalFirstTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findDentalfirstModel($id)
    {
        $query = new ActiveQuery(DentalFirstTemplate::className());
        $query->from(['a' => DentalFirstTemplate::tableName()]);
        $query->select(['a.*','b.username']);
        $query->leftJoin(['b' => User::tableName()],'{{a}}.user_id = {{b}}.id');
        $query->where(['a.id' => $id,'a.spot_id' => $this->spotId,'a.user_id' => $this->userInfo->id]);
        $model = $query->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
