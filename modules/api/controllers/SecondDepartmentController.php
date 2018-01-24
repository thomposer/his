<?php

namespace app\modules\api\controllers;

use Yii;
use yii\filters\AccessControl;
use app\modules\make_appointment\models\Appointment;
use yii\filters\VerbFilter;
use yii\db\Query;
use yii\helpers;
use app\modules\schedule\models\Scheduling;
use app\modules\user\models\UserSpot;
use app\modules\user\models\User;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\spot_set\models\Schedule;
use yii\helpers\Json;
use yii\helpers\ArrayHelper;
use app\modules\spot_set\models\OnceDepartment;
use app\modules\spot_set\models\SecondDepartmentUnion;

class SecondDepartmentController extends CommonController
{


    public function actionDepartmentSelect(){
        $postData = Yii::$app->request->post();
        $clinicId = $postData['clinic_id'];
        $query = new Query();
        $query->from(['a' => SecondDepartment::tableName()]);
        $query->select(['a.id','a.name','a.parent_id','b.name as onceName']);
        $query->leftJoin(['b' => OnceDepartment::tableName()],'{{a}}.parent_id = {{b}}.id');
        $query->leftJoin(['c' => SecondDepartmentUnion::tableName()], '{{a}}.id = {{c}}.second_department_id');
        $query->where(['a.spot_id' => $this->parentSpotId, 'c.spot_id' => $clinicId,'a.status'=>1]);
        $departmentInfo = $query->all();
        $clinicRoom = [];
        foreach($departmentInfo as $key=> $val){
            $clinicRoom[$departmentInfo[$key]['onceName']][] = $val;
        }
        $result['data'] = $clinicRoom;
        return Json::encode($result);
    }
    



}
