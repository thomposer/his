<?php

/*
 * time: 2017-6-15 10:03:06.
 * author : yu.li.
 * 将模打印全部抽出来分组  便于管理
 */

namespace app\modules\outpatient\controllers;

use app\modules\outpatient\models\AllergyOutpatient;
use app\modules\outpatient\models\FirstCheck;
use Yii;
use app\modules\outpatient\models\MaterialRecord;
use yii\web\Response;
use yii\helpers\Html;
use app\modules\pharmacy\models\PharmacyRecord;
use app\modules\spot\models\Spot;
use app\common\Common;
use app\modules\outpatient\models\DentalHistory;
use app\modules\outpatient\models\DentalHistoryRelation;
use app\modules\spot\models\SpotConfig;

trait PrintTrait
{

    /**
     * @return 其他非处方医嘱tab打印接口
     */
    public function actionMaterialPrinkInfo($id) {
        $request = Yii::$app->request;
        if (Yii::$app->request->isAjax) {
            if ($request->isGet) {
                $model = new MaterialRecord();
                $materialRecordDataProvider = MaterialRecord::getDataProvider($id);
                $dataProvider = $this->formatDataProvider($materialRecordDataProvider->query);
                Yii::$app->response->format = Response::FORMAT_JSON;
                $model->name = array_column($dataProvider,'id');
                return [
                    'title' => "打印其他清单",
                    'content' => $this->renderAjax('@outpatientCheckRecipeApplicationView', [
                        'model' => $model,
                        'dataProvider' => $dataProvider,
                        'title' => '其他',
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确定', ['class' => 'btn btn-default btn-form btn-material-check-application-print ','data-dismiss' => "modal", 'name' => $id . 'material-myshow'])
                ];
            } else {
                $materialId = Yii::$app->request->post('materialId');
                Yii::$app->response->format = Response::FORMAT_JSON;
                //未勾选直接返回错误。
                if (empty($materialId)) {
                    $this->result['errorCode'] = 1001;
                    $this->result['msg'] = '请勾选要打印的项目';
                    return $this->result;
                }

                $pharmcyRecordModel = new PharmacyRecord();
                $pharmcyRepiceInfo = $pharmcyRecordModel->getRepiceInfo($id, 5);
                $spotInfo = Spot::getSpot();
                $dataProvider = MaterialRecord::getDataProvider($id, $materialId);
                $materialRecordDataProvider = $this->formatDataProvider($dataProvider->query);
                if ($materialRecordDataProvider) {
                    $totalPrice = '';
                    foreach ($materialRecordDataProvider as $key => &$v) {
                        $v['single_total_price'] = Common::num($v['num'] * $v['price']);
                        $totalPrice += $materialRecordDataProvider[$key]['single_total_price'];
                    }
                }
                $spotConfig = SpotConfig::getConfig(['logo_img','pub_tel','spot_name','logo_shape']);
                $this->result['spotConfig'] = $spotConfig;
                $this->result['spotInfo'] = $spotInfo;
                $this->result['PharmcyRepiceInfo'] = $pharmcyRepiceInfo;
                $this->result['materialRecordDataProvider'] = $materialRecordDataProvider;
                $this->result['totalPrice'] = Common::num($totalPrice);

                return $this->result;
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @return 口腔打印
     */
    public function actionTeethPrint(){
//            1 => '口腔检查',
//            2 => '辅助检查',
//            3 => '诊断',
//            4 => '治疗方案',
//            5 => '治疗',
        Yii::$app->response->format = Response::FORMAT_JSON;
        $recordId = Yii::$app->request->post('id');
//        $recordType = Yii::$app->request->post('record_type');
        $baseDentalInfo = DentalHistory::find()->select(['type','chiefcomplaint','historypresent','pasthistory','returnvisit','advice','remarks'])->where(['record_id'=>$recordId,'spot_id'=>$this->spotId])->asArray()->one();
        $allergyInfo = AllergyOutpatient::getAllergyByRecord($recordId);
//        var_dump($allergyInfo);
        $dataDentalRelation = DentalHistoryRelation::find()->select(['type','position','content'])->where(['record_id'=>$recordId,'record_type'=>$baseDentalInfo['type'],'spot_id'=>$this->spotId])->asArray()->all();
        $baseDentalInfo['first_check'] = FirstCheck::getFirstCheckInfo($recordId);
        $defaultValue = [
            'leftTop'=>'',
            'rightTop'=>'',
            'rightBottom'=>'',
            'leftBottom'=>'',
            'content'=>''
        ];
        $rowsDefault = [
            1 => '',
            2 => '',
            3 => '',
            4 => '',
            5 => ''
        ];
        foreach ($dataDentalRelation as $v){
            $positionArray = explode(',',$v['position']);
            $rows[$v['type']][] = [
                'leftTop' => $positionArray[0],
                'rightTop' => $positionArray[1],
                'rightBottom' => $positionArray[2],
                'leftBottom' => $positionArray[3],
                'content' => $v['content']
            ];
//            unset($rows[$v['type']][0]);
        }

        $this->result['dentalBaseInfo'] = $baseDentalInfo;
        $this->result['dentalRelation'] = $rows?$rows:$rowsDefault;
        $this->result['spotInfo'] = Spot::getSpot();
        $this->result['allergyInfo'] = $allergyInfo[$recordId];
        return $this->result;

    }

    protected function formatDataProvider($query) {
        $data = $query->asArray()->all();
        if (!empty($data)) {
            foreach ($data as &$v) {
                $v['displayName'] = empty($v['specification']) ? $v['name'] : $v['name'] . '(' . $v['specification'] . ')';
            }
        }
        return $data;
    }

}
