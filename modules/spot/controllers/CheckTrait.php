<?php

namespace app\modules\spot\controllers;

use app\modules\charge\models\ChargeInfo;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\spot\models\Spot;
use app\modules\spot_set\models\CheckListClinic;
use Yii;
use app\modules\spot\models\CheckList;
use app\modules\spot\models\search\CheckListSearch;
use app\common\base\BaseController;
use yii\db\Exception;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CheckListController implements the CRUD actions for CheckList model.
 */
trait CheckTrait
{

    /**
     * Lists all CheckList models.
     * @return mixed
     */
    public function actionCheckIndex()
    {
        $searchModel = new CheckListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);
        $spotNameList = ConfigureClinicUnion::getClinicNameListString($dataProvider->keys, ChargeInfo::$checkType);
        $spotList = Spot::getSpotList(['status' => 1]);
        return $this->render('check/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'spotNameList' => $spotNameList,
            'spotList' => $spotList,
        ]);
    }

    /**
     * Displays a single CheckList model.
     * @param string $id
     * @return mixed
     */
    public function actionCheckView($id)
    {

        return $this->render('check/view', [
            'model' => $this->findCheckModel($id),
        ]);
    }

    /**
     * Creates a new CheckList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCheckCreate()
    {
        $model = new CheckList();
        $spotList=Spot::getSpotList(['status'=>1]);
        $model->scenario='unionSpotId';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans=Yii::$app->db->beginTransaction();
            try{
                $model->save();
                if(!ConfigureClinicUnion::saveInfo($model->id,$model->unionSpotId,ChargeInfo::$checkType)){
                    $dbTrans->rollBack();
                    Yii::error('保存影像学配置适用诊所失败', 'spot-checklist-create');
                    Yii::$app->getSession()->setFlash('error','保存失败');
                    return $this->redirect(['check-index']);

                }
                $dbTrans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(['check-index']);

            }catch (Exception $e){
                $dbTrans->rollBack();
                Yii::error($e->errorInfo,'spot-checklist-create');
            }

        } else {
            return $this->render('check/create', [
                'model' => $model,
                'spotList'=>$spotList,
            ]);
        }
    }

    /**
     * Updates an existing CheckList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionCheckUpdate($id)
    {
        $model = $this->findCheckModel($id);
        $spotIdList = ConfigureClinicUnion::getClinicIdList(['configure_id'=>$id,'type'=>ChargeInfo::$checkType]);
        if(!empty($spotIdList)){
            $model->unionSpotId = array_column($spotIdList,'spot_id');
        }
        $model->scenario='unionSpotId';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try{
                    $model->save();
                    if(!ConfigureClinicUnion::saveInfo($id,$model->unionSpotId,ChargeInfo::$checkType)){
                        $dbTrans->rollBack();
                        Yii::error('保存影像学配置适用诊所失败', 'spot-checklist-create');
                        Yii::$app->getSession()->setFlash('error','保存失败');
                        return $this->redirect(['check-index']);
                    }
                $dbTrans->commit();

                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(['check-index']);
            }catch (Exception $e){
                $dbTrans->rollBack();
                Yii::error($e->errorInfo,'spot-checklist-update');
            }

        } else {

            $spotList=Spot::getSpotList(['status' => 1]);
            return $this->render('check/update', [
                'model' => $model,
                'spotList' => $spotList,
            ]);
        }
    }

    /**
     * Deletes an existing CheckList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionCheckDelete($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            $model = $this->findCheckModel($id);
            $model->status = $model->status == 1 ? 2 : 1;
            $model->save();
            //停用当前机构下的所有诊所的医嘱
            CheckListClinic::updateAll(['status' => $model->status],['check_id' => $id]);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['check-index']);
        }
    }

    /**
     * Finds the CheckList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CheckList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCheckModel($id)
    {
        if (($model = CheckList::findOne(['id' => $id,'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在.');
        }
    }
}
