<?php

namespace app\modules\pharmacy\models\search;

use Yii;
use yii\helpers\Json;
use yii\data\ActiveDataProvider;
use app\modules\patient\models\PatientRecord;
use app\modules\triage\models\TriageInfo;
use app\modules\user\models\User;
use app\modules\patient\models\Patient;
use app\modules\pharmacy\models\PharmacyRecord;
use app\modules\report\models\Report;

/**
 * This is the model class for table "{{%recipe_record}}".
 *
 * @property integer $username
 * @property string $recipe_name
 * @property integer $doctor_name
 * @property integer $patients_type
 * @property string $recipe_status
 */
class PharmacyRecordSearch extends PharmacyRecord
{

    public function rules() {
        return [
            [['username', 'name', 'product_name'], 'string'],
            [['iphone', 'status'], 'integer'],
        ];
    }

    public function search($params, $pageSize = 20) {
        $query = new \yii\db\ActiveQuery(PharmacyRecord::className());
        $query->from(['pr' => PharmacyRecord::tableName()]);
        $query->select([
            'r.id', 'recipe_name' => 'pr.name', 'recipe_status' => 'pr.status', 'pharmacyRecordTime' => 'r.pharmacy_record_time', 'doctor_name' => 'u.username', 'rt.type_description', 'p.username', 'p.sex', 'p.birthday', 'pr.record_id', 'pr.create_time', 'ti.pain_score', 'ti.fall_score'
        ]);
        $query->leftJoin(['r' => PatientRecord::tableName()], '{{r}}.id={{pr}}.record_id');
        $query->leftJoin(['ti' => TriageInfo::tableName()], '{{ti}}.record_id={{r}}.id');
        $query->leftJoin(['rt' => Report::tableName()], '{{rt}}.record_id={{r}}.id');
        $query->leftJoin(['u' => User::tableName()], '{{u}}.id={{rt}}.doctor_id');
        $query->leftJoin(['p' => Patient::tableName()], '{{p}}.id={{r}}.patient_id');
        $nowTime = strtotime(date('Y-m-d'));
        $this->load($params);
        if (is_null($this->status) || empty($this->status)) {
            $recipeStatus = [1, 2, 3, 4, 5];
        } else {
            $recipeStatus = $this->status;
        }
        /* 拉取今日病人以及未发完药的病人 */
//         $andWhere = [
// //             'or',
// //             ['between', 'pr.create_time', $nowTime, $nowTime + 86400],
//             ['pr.status'=>$recipeStatus],
//         ];
//         $specialWhere = [
//             'or',
//             [
//                 'and',
//                 ['pr.status'=>1],
// //                 ['between','pr.update_time',strtotime(date('Y-m-d')),strtotime(date('Y-m-d')." 23:59:59")],
// //                ['between','pr.update_time',strtotime(date('Y-m-d')),strtotime(date('Y-m-d')." 12:00:00")],//FIXME 这里为了测试，将测试时间改为12点，后续需要改回来
//             ],
//             ['pr.status'=>[2,3]]
//         ];
        $query->andFilterWhere(['pr.status' => $recipeStatus]);
//        $query->andFilterWhere(['not between', 'pr.recipe_finish_time', 1, strtotime(date('Y-m-d'))]);
        $query->groupBy(['pr.record_id']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['r.pharmacy_record_time' => SORT_DESC],
                'attributes' => ['r.pharmacy_record_time']
            ]
        ]);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        if ($this->status == 4) {//待退药
            $query->orWhere(['pr.status' => $recipeStatus]);
        }
        $query->andFilterWhere([
            'id' => $this->id,
            'pr.spot_id' => $this->spotId,
        ]);
        $query->andFilterWhere(['like', 'p.username', trim($this->username)]);
        $query->andFilterWhere(['like', 'p.iphone', $this->iphone]);
        $query->andFilterWhere(['like', 'pr.name', trim($this->name)]);
        $query->andFilterWhere(['like', 'pr.product_name', trim($this->product_name)]);
        return $dataProvider;
    }

    public function getTodayDispenseNum($status = 3) {
        $num = self::find()->where(['spot_id' => $this->spotId, "FROM_UNIXTIME(create_time, '%Y-%m-%d')" => date("Y-m-d")])
                        ->groupBy('record_id')->having(['status' => $status])->count();
        return $num;
    }

}
