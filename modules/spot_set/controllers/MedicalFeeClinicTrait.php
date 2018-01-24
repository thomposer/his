<?php

namespace app\modules\spot_set\controllers;

use Yii;
use app\modules\spot_set\models\MedicalFeeClinic;
use app\modules\spot_set\models\search\MedicalFeeClinicSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Html;
use Exception;
use yii\helpers\Url;
use app\modules\spot\models\search\MedicalFeeSearch;
use yii\db\ActiveQuery;
use app\modules\spot\models\MedicalFee;

/**
 * MedicalFeeClinicController implements the CRUD actions for MedicalFeeClinic model.
 */
trait MedicalFeeClinicTrait
{

    /**
     * Lists all MedicalFee models.
     * @return mixed
     */
    public function actionMedicalFeeClinicIndex()
    {
        $searchModel = new MedicalFeeClinicSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);

        return $this->render('medical-fee-clinic/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MedicalFee model.
     * @param string $id
     * @return mixed
     */
    public function actionMedicalFeeClinicView($id)
    {
        $query = new ActiveQuery(MedicalFeeClinic::className());
        $query->from(['a' => MedicalFeeClinic::tableName()]);
        $query->leftJoin(['b' => MedicalFee::tableName()],'{{a}}.fee_id = {{b}}.id');
        $query->select(['a.id', 'b.remarks', 'b.price', 'b.note', 'a.status', 'a.create_time', 'a.update_time']);
        $query->where(['a.id' => $id]);
        $model = $query->one();
        return $this->render('medical-fee-clinic/view', [
            'model' =>  $model,
        ]);
    }

    /**
     * Creates a new MedicalFeeClinic model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionMedicalFeeClinicCreate()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {//弹窗
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isPost){//保存
                $dbTrans = Yii::$app->db->beginTransaction();
                try {                    
                    $db = Yii::$app->db;
                    $db->createCommand()->delete(MedicalFeeClinic::tableName(), ['spot_id' => $this->spotId])->execute();
                    $rows = [];
                    $data = $request->post('MedicalFeeId') ? $request->post('MedicalFeeId') : [];
                    foreach ($data as $value) {
                        $rows[] = array(
                            'spot_id' => $this->spotId,
                            'fee_id' => $value,
                            'status' => 1, //正常
                            'create_time' => time(),
                            'update_time' => time(),
                        );
                    }
                    $db->createCommand()->batchInsert(MedicalFeeClinic::tableName(), ['spot_id', 'fee_id', 'status', 'create_time', 'update_time'], $rows)->execute();
                    $dbTrans->commit();
                    $errorMsg = '保存成功';
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    $errorMsg = '保存失败';
                }
                return [
                        'forceMessage' => $errorMsg,
                        'forceClose' => true,
                        'forceReload'=>'#crud-datatable-pjax',
                    ];
            } else {
                $searchModel = new MedicalFeeSearch();
                $dataProvider = $searchModel->search(['MedicalFeeSearch' => ['status' => 1]], false);
                $feeInfoList = MedicalFeeClinic::getFeeInfoList();
                $feeIdList = array_column($feeInfoList, 'fee_id', 'fee_id');
                $pjax = $request->get('_pjax');
                if (!$request->isPjax) {
                    $param = time();
                } else {
                    $param = substr($pjax, 25);
                }
                return [
                    'title' => "请选择诊金",
                    'content' => $this->renderAjax('medical-fee-clinic/_form', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'feeIdList' => $feeIdList,
                        'param' => $param,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
            }
        }else{
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * Deletes an existing MedicalFeeClinic model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionMedicalFeeClinicDelete($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
        
            /*
             *   Process for ajax request
             */
            $this->findMedicalFeeClinicModel($id)->delete();
            
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
     * Finds the MedicalFeeClinic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return MedicalFeeClinic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findMedicalFeeClinicModel($id)
    {
        if (($model = MedicalFeeClinic::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
