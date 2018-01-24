<?php

namespace app\modules\outpatient\controllers;

use Yii;
use yii\web\Response;
use app\modules\outpatient\models\DentalFirstTemplate;
use yii\web\NotFoundHttpException;
use yii\db\ActiveQuery;
use app\modules\user\models\User;
use app\modules\outpatient\models\search\DentalReturnfirstTemplateSearch;
use app\modules\outpatient\models\DentalReturnvisitTemplate;

/**
 * DentalController implements the CRUD actions for DentalFirstTemplate model.
 */

trait DentalReturnVisitTrait{

    /**
     * Lists all DentalFirstTemplate models.
     * @return mixed
     */
    public function actionDentalreturnvisitIndex()
    {
        $searchModel = new DentalReturnfirstTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);

        return $this->render('/dental-returnvisit/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DentalFirstTemplate model.
     * @param integer $id
     * @return mixed
     */
    public function actionDentalreturnvisitView($id)
    {
        return $this->render('/dental-returnvisit/view', [
            'model' => $this->findDentalReturnVisitModel($id),
        ]);
    }

    /**
     * Creates a new DentalFirstTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionDentalreturnvisitCreate()
    {
        $model = new DentalReturnvisitTemplate();
        $model->type = 2;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['dentalreturnvisit-index']);
        } else {
            return $this->render('/dental-returnvisit/create', [
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
    public function actionDentalreturnvisitUpdate($id)
    {
        $model = $this->findDentalReturnVisitModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['dentalreturnvisit-index']);
        } else {
            return $this->render('/dental-returnvisit/update', [
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
    
    public function actionDentalreturnvisitDelete($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            $this->findDentalReturnVisitModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['dentalreturnvisit-index']);
        }
    }

    /**
     * Finds the DentalFirstTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DentalFirstTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findDentalReturnVisitModel($id)
    {
        $query = new ActiveQuery(DentalReturnvisitTemplate::className());
        $query->from(['a' => DentalReturnvisitTemplate::tableName()]);
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
