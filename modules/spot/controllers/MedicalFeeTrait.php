<?php

namespace app\modules\spot\controllers;

use Yii;
use app\modules\spot\models\MedicalFee;
use app\modules\spot\models\search\MedicalFeeSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\modules\spot_set\models\MedicalFeeClinic;

/**
 * MedicalFeeController implements the CRUD actions for MedicalFee model.
 */
trait MedicalFeeTrait
{

    /**
     * Lists all MedicalFee models.
     * @return mixed
     */
    public function actionMedicalFeeIndex() {
        $searchModel = new MedicalFeeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('medical-fee/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MedicalFee model.
     * @param string $id
     * @return mixed
     */
    public function actionMedicalFeeView($id) {
        return $this->render('medical-fee/view', [
                    'model' => $this->findMedicalFeeModel($id),
        ]);
    }

    /**
     * Creates a new MedicalFee model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionMedicalFeeCreate() {
        $model = new MedicalFee();
        if ($model->load(Yii::$app->request->post()) && $model->save() && $model->validate()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['medical-fee-index']);
        } else {
            return $this->render('medical-fee/create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing MedicalFee model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionMedicalFeeUpdate($id) {
        $model = $this->findMedicalFeeModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save() && $model->validate()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['medical-fee-index']);
        } else {
            return $this->render('medical-fee/update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing MedicalFee model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionMedicalFeeDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {

            /*
             *   Process for ajax request
             */
            $this->findMedicalFeeModel($id)->delete();

            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['medical-fee-index']);
        }
    }

    /**
     * 停用或启用
     * @param string $id
     * @return mixed
     */
    public function actionMedicalFeeUpdateStatus($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $db = Yii::$app->db;
                $model = $this->findMedicalFeeModel($id);
                if ($model->status != 1) {
                    $model->status = 1;
                    $model->save();
                } else {
                    $model->status = 2;
                    $model->save();
                }
                $db->createCommand()->update(MedicalFeeClinic::tableName(), ['status' => $model->status], ['fee_id' => $id])->execute();
                $dbTrans->commit();
                return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
            } catch (Exception $e) {
                $dbTrans->rollBack();
            }
            return [
                'forceClose' => true,
                'forceType' => 2,
                'forceMessage' => ($model->status ? '启用' : '停用') . '失败',
                'forceReload' => '#crud-datatable-pjax'
            ];
        } else {
            return $this->redirect(['medical-fee-index']);
        }
    }

    /**
     * Finds the MedicalFee model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return MedicalFee the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findMedicalFeeModel($id) {
        if (($model = MedicalFee::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

}
