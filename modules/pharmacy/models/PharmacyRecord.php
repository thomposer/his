<?php

namespace app\modules\pharmacy\models;

use app\modules\charge\models\ChargeInfo;
use app\modules\outpatient\models\AllergyOutpatient;
use app\modules\outpatient\models\CheckRecord;
use app\modules\outpatient\models\ConsumablesRecord;
use app\modules\outpatient\models\CureRecord;
use app\modules\outpatient\models\FirstCheck;
use app\modules\outpatient\models\InspectRecord;
use app\modules\outpatient\models\MaterialRecord;
use app\modules\outpatient\models\RecipeRecord;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use app\modules\report\models\Report;
use app\modules\spot\models\RecipeList;
use app\modules\spot_set\models\Room;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\stock\models\Stock;
use app\modules\stock\models\StockInfo;
use app\modules\triage\models\TriageInfo;
use app\modules\user\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%recipe_record}}".
 *
 * @property integer $username
 * @property string $recipe_name
 * @property integer $doctor_name
 * @property integer $type_description
 * @property string $recipe_status
 */
class PharmacyRecord extends \app\modules\outpatient\models\RecipeRecord
{

    public $username; //患者信息
    public $pr_id; 
    public $iphone; //手机号
    public $recipe_name; //药品名称
    public $doctor_name; //开单医生
    public $type_description; //预约服务
    public $recipe_status; //发药状态
    public $sex;
    public $birthday; 
    public $storage; //库存
    public $batch_number; 
    public $expire_time; 
    public $need_num; //批次发药数量
    public $batch_id; //批次ID
    public $recipe_record_id; //ID
    public $recipe_record; 
    public $storage_limit; 
    public $charge_status; 
    public $recipe_finish_time;
    public $idArr; //发药id列表
    public $pharmacyRecordTime; //药房列表开单时间
    public $pain_score; //疼痛评分
    public $fall_score; //跌倒评分
    public $recipeList;
    public static $getUsed = [
        '1' => '口服',
        '2' => '注射'
    ];
    public static $getFrequency = [
        '1' => '每天一次',
        '2' => '每天二次',
        '3' => '每天三次'
    ];
    public static $getUnit = [
        '1' => '袋',
        '2' => '片'
    ];
    public static $getType = [
        '1' => '本门诊部',
        '2' => '外购'
    ];

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['need_num'], 'validateNeedNum'],
            [['recipeList'], 'required', 'on' => 'printLabel'],
        ];
    }

    public function attributeLabels() {
        return [
            'id' => 'ID',
            'username' => '患者信息',
            'iphone' => '手机号',
            'recipe_name' => '药品',
            'product_name' => '商品名称',
            'doctor_name' => '接诊医生',
            'type_description' => '服务类型',
            'recipe_status' => '发药状态',
            'name' => '药品名称',
            'dose' => '剂量',
            'used' => '用法',
            'frequency' => '用药频次',
            'day' => '天数',
            'num' => '数量',
            'unit' => '单位',
            'specification' => '规格',
            'description' => '说明',
            'type' => '取药地点',
            'remark' => '用药须知',
            'storage' => '剩余数量',
            'batch_number' => '批次',
            'expire_time' => '有效期',
            'charge_status' => '收费状态',
            'recipe_record_id' => 'id',
            'update_time' => '发药时间',
            'recipe_finish_time' => '发药状态变更时间',
            'dose_unit' => '剂量单位',
            'dosage_form' => '剂型',
            'price' => '金额（元）',
            'pharmacyRecordTime' => '开单时间',
            'recipeList' => '处方项目',
            'status'=>'状态',
            'usage'=>'用法用量',
        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['printLabel'] = ['recipeList'];
        return $parent;
    }

    /*
     * 获取处方的药名  以逗号隔开
     */

    public static function getRecipeArray($record_id) {
        $record = self::find()->where(['record_id' => $record_id, 'spot_id' => self::$staticSpotId])->asArray()->all();
        $name = [];
        foreach ($record as $v) {
            if (!empty($v['specification'])) {
                $name[] = $v['name'] . '（' . $v['specification'] . '）';
            } else {
                $name[] = $v['name'];
            }
        }
        return implode(',', $name);
    }

    /**
     *
     * @param int $record_id  记录流水ID
     * @param int $status  状态
     * @return int 获取发药状态的数量
     */
    public static function getDispenseNum($record_id, $status = 3) {
        $num = self::find()->where(['record_id' => $record_id, 'status' => $status, 'spot_id' => self::$staticSpotId])->count();
        return $num;
    }

    /**
     * 
     * @param integer $id 就诊流水id
     * @param number $status 处方执行状态
     * @return \yii\data\ActiveDataProvider
     */
    public function findRecipeRecordDataProvider($id, $status = 3) {
        $query = new \yii\db\ActiveQuery(PharmacyRecord::className());
        $query->from(['pr' => PharmacyRecord::tableName()]);
        $fields = [
            'pr.id', 'pr.name', 'pr.unit', 'pr.price','pr.product_name', 'pr.dose', 'pr.used', 'pr.frequency', 'pr.day', 'pr.num',
            'pr.description', 'pr.type', 'pr.status', 'pr.remark', 'pr.dose_unit', 'pr.dosage_form', 'pr.drug_type','pr.skin_test_status', 'pr.skin_test', 'pr.specification','pr.high_risk', 'd.cure_result', 'cureListName' => 'd.name', 'cureType' => 'd.type'
        ];
        $query->leftJoin(['c' => RecipeList::tableName()], '{{pr}}.recipe_id = {{c}}.id');
        $query->leftJoin(['d' => CureRecord::tableName()], '{{pr}}.cure_id = {{d}}.id');
//        $query->leftJoin(['e' => CureList::tableName()], '{{pr}}.curelist_id = {{e}}.id');
        $chargeRecord = ChargeInfo::getChargeRecordNum($id);
        $where = ['pr.spot_id' => $this->spotId, 'pr.record_id' => $id, 'pr.status' => $status];
        if ($chargeRecord) {
//            $where['ci.type'] = ChargeInfo::$recipeType;
            $fields['charge_status'] = 'ci.status';
            $query->leftJoin(['ci' => ChargeInfo::tableName()], '{{ci}}.outpatient_id={{pr}}.id');
        }
        $query->select($fields);
        $query->where($where);
//        $query->andWhere([ "FROM_UNIXTIME(pr.create_time, '%Y-%m-%d')" => date("Y-m-d")]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
    }

    public static function findRecipeRecordSkinTest($id) {
        $query = new Query();
        $query->from(['pr' => PharmacyRecord::tableName()]);
        $fields = [
            'pr.id', 'pr.skin_test_status', 'pr.skin_test', 'd.cure_result', 'cureListName' => 'd.name', 'cureType' => 'd.type'
        ];
        $query->select($fields);
        $query->leftJoin(['d' => CureRecord::tableName()], '{{pr}}.cure_id = {{d}}.id');
        $query->where(['pr.spot_id' => self::$staticSpotId, 'pr.record_id' => $id]);
        return $query->all();
    }

    /*
     * 按批次发药
     */

    public function findBatchDataProvider($idArr) {
        $query = new \yii\db\ActiveQuery(PharmacyRecord::className());
        $query->from(['pr' => PharmacyRecord::tableName()]);
        $query->select(['pr_id' => 'pr.id', 'pr.specification', 'pr.skin_test_status', 'recipe_name' => 'pr.name', 'pr.unit', 'pr.price', 'pr.dose', 'pr.used', 'pr.frequency', 'pr.day', 'recipe_record_id' => 'pr.id',
            'pr.num', 'pr.description', 'pr.type', 'pr.status', 'pr.remark', 'storage' => 'si.num', 'si.batch_number', 'si.expire_time', 'batch_id' => 'si.id','pr.high_risk'
        ]);
        $query->leftJoin(['si' => StockInfo::tableName()], '{{si}}.recipe_id={{pr}}.recipe_id');
        $query->leftJoin(['s' => Stock::tableName()], '{{si}}.stock_id={{s}}.id');
        $query->where(['si.spot_id' => $this->spotId, 'pr.id' => $idArr, 's.status' => 1, 'pr.type' => 1]);
        $query->andWhere(['>', 'si.num', 0]);
        $query->andWhere(['>=', 'si.expire_time', strtotime(date('Y-m-d'))]);
        $query->andWhere(['s.status' => 1]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'defaultOrder' => [
                    'pr.num' => SORT_ASC,
                    'pr.id' => SORT_ASC,
                    'si.expire_time' => SORT_ASC,
                    
                ],
                'attributes' => ['pr.num','pr.id','si.expire_time']
            ]
        ]);
        return $dataProvider;
    }

    public static function getBillingTime($record_id, $type = 1, $listId = 0) {
        $time = 0;
        $where['record_id'] = $record_id;
        switch ($type) {
            case 1:
                if ($listId) {
                    $where['id'] = $listId;
                }
                $time = InspectRecord::find()->where($where)->max('create_time');
                break;
            case 2:
                if ($listId) {
                    $where['id'] = $listId;
                }
                $time = CheckRecord::find()->where($where)->max('create_time');
                break;
            case 3:
                $time = CureRecord::find()->where($where)->max('create_time');
                break;
            case 4:
                $time = PatientRecord::find()->select('pharmacy_record_time')->where(['id' => $record_id, 'spot_id' => self::$staticSpotId])->asArray()->one()['pharmacy_record_time'];
                break;
            case 5:
                $time = MaterialRecord::find()->where($where)->max('create_time');
                break;
            case 8://医疗耗材
                $time = ConsumablesRecord::find()->where($where)->max('create_time');
                break;
            default:
                $time = 0;
                break;
        }
        return $time;
    }

    /**
     *
     * @param 就诊流水id $record_id
     * @param 类型[1实验室 2影像学 3治疗 4处方]
     * @return multitype:string 获取处方的开单医生和过敏史  以及诊断信息
     */
    public static function getRepiceInfo($record_id, $type = 1, $listId = 0) {
        $cacheKey = Yii::getAlias('@billingDoctor') . $record_id . '_' . $type . '_' . $listId;
        //$repiceInfo = Yii::$app->cache->get($cacheKey);
        $repiceInfo = '';
        if (empty($repiceInfo)) {
            $repiceInfo = [
                'doctor' => '',
                'time' => '',
                'room' => '',
                'second_department' => '',
                'first_check' => '',
                'update_time' => '',
                'update_time_formate' => ''
            ];

            $query = new Query();
            $query->from(['a' => TriageInfo::tableName()]);
            $query->select(['b.doctor_id', 'a.meditation_allergy', 'a.food_allergy', 'a.room_id', 'a.diagnosis_time', 'a.first_check', 'd.name', 'c.update_time', 'e.username', 'f.clinic_name']);
            $query->leftJoin(['b' => Report::tableName()], '{{a}}.record_id = {{b}}.record_id');
            $query->leftJoin(['c' => PharmacyRecord::tableName()], '{{a}}.record_id = {{c}}.record_id');
            $query->leftJoin(['d' => SecondDepartment::tableName()], '{{b}}.second_department_id = {{d}}.id');
            $query->leftJoin(['e' => User::tableName()], '{{b}}.doctor_id = {{e}}.id');
            $query->leftJoin(['f' => Room::tableName()], '{{a}}.room_id = {{f}}.id');
            $query->where(['a.record_id' => $record_id]);
            $result = $query->one();


            $repiceInfo['second_department'] = $result['name'];
            $repiceInfo['doctor'] = $result['username'] ? $result['username'] : '';
            $billingTime = self::getBillingTime($record_id, $type, $listId);
            $repiceInfo['time'] = $billingTime ? date('Y-m-d H:i:s', $billingTime) : 0;
            $repiceInfo['room'] = $result['clinic_name'] ? $result['clinic_name'] : '';
            $repiceInfo['first_check'] = $result ? $result['first_check'] : '';
            $repiceInfo['update_time'] = $result ? $result['update_time'] : '';
            $repiceInfo['food_allergy'] = $result ? $result['food_allergy'] : '';
            $repiceInfo['meditation_allergy'] = $result ? $result['meditation_allergy'] : '';
            $repiceInfo['update_time_formate'] = $result ? date('Y-m-d H:i:s', $repiceInfo['update_time']) : '';
            $repiceInfo['first_check'] = FirstCheck::getFirstCheckInfo($record_id);
            $repiceInfo['allergy'] = AllergyOutpatient::getAllergyByRecord($record_id);
            //Yii::$app->cache->set($cacheKey, $repiceInfo, Yii::getAlias('@cachePrintTime'));
        }
        return $repiceInfo;
    }

    /**
     * @return 验证按批次发药的 数量和库存
     * @param unknown $attribute
     * @param unknown $params
     */
    public function validateNeedNum($attribute, $params) {
        $recipeRecordObj = json_decode($this->recipe_record, true);
        $idArr = json_decode($this->idArr, true);
        $countList = $this->need_num;
        $recipeId = $this->recipe_record_id;
        $storage_limit = $this->storage_limit;
        $batchId=$this->batch_id;
        if (count($this->need_num) > 0) {
            $list = RecipeRecord::find()->select(['id', 'num'])->where(['id' => $idArr, 'spot_id' => $this->spotId, 'status' => 3, 'type' => 1])->asArray()->all();
            foreach ($list as $k => $v) {
                $recipeRecordObj[$v['id']] = $v['num'];
            }
            foreach ($countList as $key => $v1) {
                $storage_limit[$batchId[$key]] -= $v1;
                if ($storage_limit[$batchId[$key]] < 0) {
                    $this->addError($attribute, '您输入的数量有误，请重新输入');
                    break;
                }
                $recipeRecordObj[$recipeId[$key]] = $recipeRecordObj[$recipeId[$key]] - $v1;
            }
            // 判断药品数量是否符合
            foreach ($recipeRecordObj as $v2) {
                if ($v2 !== 0) {
                    $this->addError($attribute, '您输入的数量有误，请重新输入');
                    break;
                }
            }
        } else {
            $this->addError($attribute, '您输入的数量有误，请重新输入');
        }
    }

    protected function getStorageLimit($id) {
        $stockInfo = StockInfo::find()->select(['num', 'recipe_id'])->where(['id' => $id])->asArray()->one();
        return $stockInfo['num'];
    }

    /**
     * 
     * @param 就诊记录id集合 $recordId
     * @return string 返回对应就诊记录的药品信息，以及各个状态的数量
     */
    public static function getRecipeRecordInfo($recordId, $status = 0) {
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['a.id', 'a.record_id', 'a.name', 'a.specification', 'a.status','a.high_risk']);
        $query->where(['a.record_id' => $recordId, 'a.spot_id' => self::$staticSpotId]);
//        if ($status != 4) {
//            $query->andFilterWhere(['not between', 'a.recipe_finish_time', 1, strtotime(date('Y-m-d'))]);
//        }
        $result = $query->all();
        $info = [];
        foreach ($result as $v) {
            $v['name'] = Html::encode($v['name']);
            $v['specification'] = Html::encode($v['specification']);
            if($v['high_risk'] == 1){
                $v['name'] = '<span class="high-risk">高危</span>'.$v['name'];
            }
            if (!empty($v['specification'])) {
                $info['name'][$v['record_id']][] = $v['name'] . '（' . $v['specification'] . '）';
            } else {
                $info['name'][$v['record_id']][] = $v['name'];
            }
            $info['status'][$v['record_id']][$v['status']] ++;
        }
        return $info;
    }

    /**
     * 
     * @param type $recordId 流水ID
     * @return type 获取打印标签弹窗的列表数据 只拉取内购的
     */
    public static function getRecipeListByRecord($recordId,$status) {
        $record = self::find()->select(['id', 'name', 'specification'])->where(['record_id' => $recordId, 'spot_id' => self::$staticSpotId, 'type' => 1,'status' => $status])->asArray()->all();
        foreach ($record as &$v) {
            if (!empty($v['specification'])) {
                $v['name'] = $v['name'] . '（' . $v['specification'] . '）';
            }
        }
        return $record;
    }

    /**
     * 
     * @param type $id 处方ID
     * @return type 获取处方标签的打印内容
     */
    public static function printRecipeLabelData($id) {
        $query = new Query();
        $data = $query->from(['a' => self::tableName()])
                ->select(['recipeName' => 'a.name', 'a.unit', 'a.num', 'a.specification', 'a.used', 'a.dose_unit', 'a.frequency', 'a.day', 'a.remark', 'userName' => 'c.username', 'patientNumber' => 'c.patient_number', 'c.sex', 'c.birthday', 'a.dose','productName' => 'a.product_name'])
                ->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id={{b}}.id')
                ->leftJoin(['c' => Patient::tableName()], '{{b}}.patient_id={{c}}.id')
                ->where(['a.id' => $id, 'a.spot_id' => self::$staticSpotId])
                ->all();
        $res = [];
        foreach ($data as $val) {
            $res[] = [
                'userName' => $val['userName'],
                'patientNumber' => $val['patientNumber'],
                'sex' => isset(Patient::$getSex[$val['sex']]) ? Patient::$getSex[$val['sex']] : '',
                'age' => Patient::dateDiffage($val['birthday']) . '(' . date('Y-m-d', $val['birthday']) . ')',
                'recipeName' => $val['recipeName'],
                'productName' => $val['productName'],
                'unit' => $val['num'] . RecipeList::$getUnit[$val['unit']],
                'specification' => $val['specification'], 
                'used' => RecipeList::$getDefaultUsed[$val['used']], 
                'frequency' => $val['dose'] . RecipeList::$getDoseUnit[$val['dose_unit']] . ','.RecipeList::$getDefaultUsed[$val['used']].','. RecipeList::$getDefaultConsumption[$val['frequency']] . ',' . $val['day'] . '天', 
                'remark' => Html::encode($val['remark'])
            ];
        }
        return $res;
    }

}
