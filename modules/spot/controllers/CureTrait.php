<?php

namespace app\modules\spot\controllers;

use app\common\base\BaseController;
use app\modules\spot\models\CureList;
use app\modules\spot\models\search\CureListSearch;
use app\modules\spot_set\models\ClinicCure;
use Yii;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use app\modules\spot\models\Spot;
use app\modules\charge\models\ChargeInfo;
use yii\db\Exception;
use app\modules\spot\models\ConfigureClinicUnion;

/**
 * CureListController implements the CRUD actions for CureList model.
 */
trait CureTrait
{

    /**
     * Lists all CureList models.
     * @return mixed
     */
    public function actionCureIndex()
    {
        $searchModel = new CureListSearch();
        $dataProvider = $searchModel -> search(Yii ::$app -> request -> queryParams, $this -> pageSize);
        $spotNameList = ConfigureClinicUnion::getClinicNameListString($dataProvider->keys, ChargeInfo::$cureType);
        $spotList = Spot::getSpotList(['status' => 1]);
        return $this -> render('cure/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'spotNameList' => $spotNameList,
            'spotList' => $spotList,
        ]);
    }

    /**
     * Displays a single CureList model.
     * @param string $id
     * @return mixed
     */
    public function actionCureView($id)
    {
        return $this -> render('cure/view', [
            'model' => $this -> findCureModel($id),
        ]);
    }

    /**
     * Creates a new CureList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCureCreate()
    {
        $model = new CureList();
        $model->scenario = 'update';
        if ($model -> load(Yii ::$app -> request -> post()) && $model ->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $model->save();
                if (!ConfigureClinicUnion::saveInfo($model->id, $model->unionSpotId, ChargeInfo::$cureType)) {
                    $dbTrans->rollBack();
                    Yii::error('保存治疗配置适用诊所失败', 'spot-curelist-create');
                    Yii::$app->getSession()->setFlash('error','保存失败');
                    return $this->redirect(['cure-index']);
                }
                $dbTrans->commit();
                Yii ::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(['cure-index']);
            } catch (Exception $e) {
                $dbTrans->rollBack();
                Yii::error($e->errorInfo,'spot-curelist-create');
            }
        } else {
            $spotList = Spot::getSpotList(['status' => 1]);
            return $this -> render('cure/create', [
                'model' => $model,
                'spotList' => $spotList,
            ]);
        }
    }

    /**
     * Updates an existing CureList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionCureUpdate($id)
    {
        $model = $this -> findCureModel($id);
        $model->scenario = 'update';
        $spotIdList = ConfigureClinicUnion::getClinicIdList(['configure_id' => $id,'type' => ChargeInfo::$cureType]);
        if(!empty($spotIdList)){
            $model->unionSpotId = array_column($spotIdList, 'spot_id');
        }

        if ($model->load(Yii ::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $model->save();
                if (!ConfigureClinicUnion::saveInfo($model->id, $model->unionSpotId, ChargeInfo::$cureType)) {
                    $dbTrans->rollBack();
                    Yii::error('保存治疗配置适用诊所失败', 'spot-curelist-update');
                    Yii::$app->getSession()->setFlash('error', '保存失败');
                    return $this->redirect(['cure-index']);
                }
                $dbTrans->commit();
                Yii ::$app->getSession()->setFlash('success', '保存成功');
                return $this->redirect(['cure-index']);
            } catch (Exception $e) {
                $dbTrans->rollBack();
                Yii::error($e->errorInfo, 'spot-curelist-update');
            }
        } else {
            $spotList = Spot::getSpotList(['status' => 1]);
            return $this -> render('cure/update', [
                'model' => $model,
                'spotList' => $spotList,
            ]);
        }
    }

    /**
     * Deletes an existing CureList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionCureDelete($id)
    {
        $request = Yii ::$app -> request;
        if ($request -> isAjax) {
            
            $model = $this->findCureModel($id);
            $model->status = $model->status == 1 ? 2 : 1;
            $model->save();
            ClinicCure ::updateCureStatus($id, $model->status);

            Yii ::$app -> response -> format = Response::FORMAT_JSON;

            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this -> redirect(['cure-index']);
        }
    }

    /**
     * Finds the CureList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CureList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCureModel($id)
    {
        if (($model = CureList ::findOne(['id' => $id, 'spot_id' => $this -> parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在.');
        }
    }
}
