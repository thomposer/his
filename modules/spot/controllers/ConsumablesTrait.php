<?php

namespace app\modules\spot\controllers;

use Yii;
use yii\web\Response;
use app\modules\spot\models\Consumables;
use app\modules\spot\models\search\ConsumablesSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\spot_set\models\ConsumablesClinic;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;
use yii\db\Exception;
use app\modules\spot\models\Spot;

/**
 * ConsumablesController implements the CRUD actions for Consumables model.
 */
trait ConsumablesTrait
{
    /**
     * Lists all Consumables models.
     * @return mixed
     */
    public function actionConsumablesIndex()
    {
        $searchModel = new ConsumablesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);
        $spotNameList = ConfigureClinicUnion::getClinicNameListString($dataProvider->keys, ChargeInfo::$consumablesType);
        $spotList = Spot::getSpotList(['status' => 1]);
        
        return $this->render('consumables/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'spotNameList' => $spotNameList,
            'spotList' => $spotList
        ]);
    }

    /**
     * Displays a single Consumables model.
     * @param integer $id
     * @return mixed
     */
    public function actionConsumablesView($id)
    {
        return $this->render('consumables/view', [
            'model' => $this->findConsumablesModel($id),
        ]);
    }

    /**
     * Creates a new Consumables model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionConsumablesCreate()
    {
        $model = new Consumables();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $model->save();
                if (!ConfigureClinicUnion::saveInfo($model->id, $model->unionSpotId, ChargeInfo::$consumablesType)) {
                    $dbTrans->rollBack();
                    Yii::error('保存医疗耗材配置适用诊所失败', 'spot-consumable-create');
                    Yii::$app->getSession()->setFlash('error','保存失败');
                    return $this->redirect(['consumables-index']);
                }
                
                $dbTrans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(['consumables-index']);
                
            }catch (Exception $e){
                $dbTrans->rollBack();
                Yii::error($e->errorInfo,'spot-consumable-create');
            }
            
        } else {
            $spotList = Spot::getSpotList(['status' => 1]);
            return $this->render('consumables/create', [
                'model' => $model,
                'spotList' => $spotList
            ]);
        }
    }

    /**
     * Updates an existing Consumables model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionConsumablesUpdate($id)
    {
        $model = $this->findConsumablesModel($id);
        $spotIdList = ConfigureClinicUnion::getClinicIdList(['configure_id' => $id,'type' => ChargeInfo::$consumablesType]);
        if(!empty($spotIdList)){
            $model->unionSpotId = array_column($spotIdList, 'spot_id');
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $model->save();
                if (!ConfigureClinicUnion::saveInfo($model->id, $model->unionSpotId, ChargeInfo::$consumablesType)) {
                    $dbTrans->rollBack();
                    Yii::error('保存医疗耗材配置适用诊所失败', 'spot-consumable-update');
                    Yii::$app->getSession()->setFlash('error','保存失败');
                    return $this->redirect(['consumables-index']);
                }
                
                $dbTrans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(['consumables-index']);
                
            }catch (Exception $e){
                $dbTrans->rollBack();
                Yii::error($e->errorInfo,'spot-consumable-update');
            }
        } else {
            $spotList = Spot::getSpotList(['status' => 1]);
            return $this->render('consumables/update', [
                'model' => $model,
                'spotList' => $spotList
            ]);
        }
    }

    /**
     * Delete an existing Consumables model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    
    public function actionConsumablesUpdateStatus($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            $model = $this->findConsumablesModel($id);
            $model->status = $model->status == 1 ? 2 : 1;
            $model->scenario = 'updateStatus';
            $model->save();
            ConsumablesClinic::updateAll(['status' => $model->status],['consumables_id' => $id]);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['consumables-index']);
        }
    }

    /**
     * Finds the Consumables model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Consumables the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findConsumablesModel($id)
    {
        if (($model = Consumables::findOne(['id' => $id,'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
