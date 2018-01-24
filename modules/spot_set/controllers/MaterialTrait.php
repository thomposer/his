<?php

namespace app\modules\spot_set\controllers;

use app\common\base\BaseController;
use app\modules\spot_set\models\search\MaterialSearch;
use app\modules\spot_set\models\Material;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * MaterialController implements the CRUD actions for Material model.
 */
trait MaterialTrait
{

    /**
     * Lists all Material models.
     * @return mixed
     */
    public function actionMaterialIndex()
    {
        $searchModel = new MaterialSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);

        return $this->render('material/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Material model.
     * @param string $id
     * @return mixed
     */
    public function actionMaterialView($id)
    {
        return $this->render('material/view', [
            'model' => $this->findMaterialModel($id),
        ]);
    }

    /**
     * Creates a new Material model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionMaterialCreate()
    {
        $model = new Material();
        $model->attribute = 1;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['material-index']);
        } else {
            return $this->render('material/create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Material model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionMaterialUpdate($id)
    {
        $model = $this->findMaterialModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success','保存成功');
            return $this->redirect(['material-index']);
        } else {
            
            return $this->render('material/update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Delete an existing Material model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    
    public function actionMaterialDelete($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            $this->findMaterialModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['material-index']);
        }
    }

    /**
     * Finds the Material model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Material the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findMaterialModel($id)
    {
        if (($model = Material::findOne(['id' => $id,'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
