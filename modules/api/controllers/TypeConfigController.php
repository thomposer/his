<?php


namespace app\modules\api\controllers;

use Yii;
use yii\filters\VerbFilter;
use app\modules\spot\models\OrganizationType;
use yii\helpers\ArrayHelper;

class TypeConfigController extends CommonController
{

    public function behaviors() {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-type-time' => ['post'],
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }

    /**
     * get-type-time
     * @param int $id 机构服务类型id
     * @return int 预约服务时长
     * @desc 获取机构服务时长
     */
    public function actionGetTypeTime() {
        $id = Yii::$app->request->post('id');
        $this->result['time'] = OrganizationType::getSpotType('id = '.$id)[0]['time'];
//        $this->result['list'] = OrganizationType::$getTime;
        return json_encode($this->result);
    }
}
