<?php

namespace app\modules\spot\controllers;

use Yii;
use app\modules\spot\models\InspectItem;
use app\modules\spot\models\search\InspectItemSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\spot\models\InspectItemUnion;
use yii\web\Response;

/**
 * InspectItemController implements the CRUD actions for InspectItem model.
 */
trait ItemTrait
{

    /**
     * Lists all InspectItem models.
     * @return mixed
     */
    public function actionItemIndex() {
        $searchModel = new InspectItemSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('item/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single InspectItem model.
     * @param string $id
     * @return mixed
     */
    public function actionItemView($id) {
        return $this->render('item/view', [
                    'model' => $this->findItemModel($id),
        ]);
    }

    /**
     * Creates a new InspectItem model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionItemCreate() {
        $model = new InspectItem();
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['item-index']);
        } else {
            return $this->render('item/create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing InspectItem model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionItemUpdate($id) {
        $model = $this->findItemModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['item-index']);
        } else {
            return $this->render('item/update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing InspectItem model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionItemDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $status = (int) Yii::$app->request->get('status');
            $s = $status == 1 ? 2 : 1;
            InspectItem::updateAll(['status' => $s], ['id' => $id, 'spot_id' => $this->parentSpotId]);
            //删除检验医嘱的关联当前项目
//            InspectItemUnion::deleteAll(['item_id' => $id]);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['item-index']);
        }
    }

    /**
     * Finds the InspectItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return InspectItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findItemModel($id) {
        if (($model = InspectItem::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在.');
        }
    }

}
