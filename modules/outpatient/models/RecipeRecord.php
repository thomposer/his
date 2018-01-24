<?php

namespace app\modules\outpatient\models;

use app\modules\spot_set\models\ClinicCure;
use Yii;
use yii\helpers\Json;
use yii\db\Query;
use app\modules\spot\models\RecipeList;
use app\modules\charge\models\ChargeInfo;
use app\modules\spot\models\CureList;
use app\modules\outpatient\models\CureRecord;
//use app\modules\report\models\Report;
use app\modules\charge\models\ChargeRecord;
use Exception;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%recipe_record}}".
 *
 * @property string $id
 * @property string $record_id
 * @property string $spot_id
 * @property integer $recipe_id
 * @property string $dose
 * @property integer $used
 * @property integer $frequency
 * @property string $day
 * @property string $num
 * @property integer $unit
 * @property string $description
 * @property integer $type
 * @property string $name
 * @property integer $price
 * @property integer $status
 * @property integer $report_time 报告时间
 * @property integer $report_user_id 报告人
 * @property integer $dose_unit 剂量单位
 * @property integer $medicine_description_id 用药指南id
 * @property integer $skin_test_status 皮试状态(0-没，1-是,2-否)
 * @property integer $recipe_finish_time  发药完成中状态变更时间
 * @property string $skin_test 皮试内容
 * @property string $tag_id 标签id
 * @property string $create_time
 * @property string $update_time
 */
class RecipeRecord extends \app\common\base\BaseActiveRecord
{

    public $recipe_id;
    public $recipeName;
    public $deleted;
    public $totalNum; //库存剩余总量
    public $billingTime;
    public $usage;//用法用量

    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%recipe_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['record_id', 'spot_id'], 'required'],
            [['billingTime'], 'required', 'on' => 'makeup'],
            [['record_id', 'spot_id', 'create_time', 'update_time', 'status', 'report_time', 'report_user_id', 'recipe_finish_time', 'medicine_description_id', 'tag_id', 'cure_id','high_risk'], 'integer'],
            [['recipe_id'], 'validateInteger'],
            ['description', 'validateDescription'],
            [['recipeName', 'totalNum', 'unit', 'dosage_form', 'used', 'frequency', 'dose', 'type', 'day', 'num', 'deleted', 'dose_unit', 'curelist_id'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['specification', 'skin_test'], 'string', 'max' => 64],
            [['specification', 'skin_test'], 'default', 'value' => ''],
            [['price'], 'number'],
            [['price', 'report_time', 'report_user_id', 'medicine_description_id', 'recipe_finish_time', 'tag_id', 'curelist_id', 'cure_id','high_risk','package_record_id','skin_test_status'], 'default', 'value' => '0'],
        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['makeup'] = ['record_id', 'spot_id', 'name', 'price', 'unit', 'used', 'frequency', 'dose', 'type', 'day', 'num', 'deleted', 'remark', 'recipe_id', 'billingTime', 'dose_unit', 'skin_test_status', 'skin_test','description'];
        $parent['recipe-back'] = ['id'];
        return $parent;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'record_id' => '流水ID',
            'spot_id' => '诊所ID',
            'recipe_id' => '处方ID',
            'name' => '处方名称',
            'specification' => '规格',
            'price' => '零售价',
            'status' => '状态',
            'dose' => '剂量',
            'used' => '用法',
            'frequency' => '用药频次',
            'day' => '天数',
            'num' => '数量',
            'unit' => '单位',
            'report_time' => '报告时间',
            'report_user_id' => '报告人',
            'description' => '填写说明',
            'type' => '类型(1-本院,2-外购)',
            'create_time' => '创建时间',
            'billingTime' => '开单时间',
            'update_time' => '更新时间',
            'recipeName' => '新增药品',
            'dose_unit' => '剂量单位',
            'remark' => '备注',
            'medicine_description_id' => '用药指南id',
            'dosage_form' => '剂型',
            'tag_id' => '标签id',
            'curelist_id' => '治疗医嘱配置id',
            'cure_id' => '治疗医嘱id',
        ];
    }

    public static $getStatus = [
        1 => '已发药',
        2 => '发药中',
        3 => '待发药',
        4 => '待退药',
        5 => '已退药'
    ];
    public static $getStatusOtherDesc = [
        1 => '已发药',
        3 => '待发药',
        4 => '待退药',
        5 => '已退药'
    ];

    /**
     * @return 皮试状态
     * @var 1-需要,2-免
     */
    public static $getSkinTestStatus = [
        1 => '需要',
        2 => '免'
    ];

    public function validateInteger($attribute, $params) {
        $rows = [];
        $totalNum = [];
        if (count($this->recipe_id) > 0) {
            $alreadyChargeId = array();
            foreach ($this->recipe_id as $key => $v) {
                try {
                    $list = Json::decode($v);
                } catch (Exception $e) {
                    $this->addError($attribute, '参数错误');
                }
                if ($this->deleted[$key] != 1) {
                    if (!preg_match("/^\s*[+-]?\d+\s*$/", $list['id'])) {
                        $this->addError($attribute, '参数错误');
                    } else if ($this->dose[$key] < 0 || $this->dose[$key] > 1000) {
                        $this->addError($attribute, '剂量必须在0~1000范围内');
                    } else if ($this->dose_unit[$key] == '' || $this->dose_unit[$key] == 0) {
                        $this->addError($attribute, '剂量不能为空');
                    } else if ($this->day[$key] < 0 || $this->day[$key] > 100) {
                        $this->addError($attribute, '天数必须在0~100范围内');
                    } else if ($this->num[$key] <= 0 || $this->num[$key] > 100) {
                        $this->addError($attribute, '数量必须在1~100范围内');
                    } else if (!preg_match("/^\s*[-+]?[0-9]*\.?[0-9]+([eE][-+]?[0-9]+)?\s*$/", $this->dose[$key]) || !isset($this->dose[$key])) {
                        $this->addError($attribute, '剂量必须为一个数字');
                    } else if (strlen(explode('.', $this->dose[$key])[1]) > 7) {
                        $this->addError($attribute, '剂量最多可输入7位小数');
                    } else if (!preg_match("/^\s*[+-]?\d+\s*$/", $this->day[$key]) || !isset($this->day[$key])) {
                        $this->addError($attribute, '天数必须为一个数字');
                    } else if (!preg_match("/^\s*[+-]?\d+\s*$/", $this->num[$key]) || !isset($this->num[$key])) {
                        $this->addError($attribute, '数量必须为一个整数');
                    } else if ($this->used[$key] == 0) {
                        $this->addError($attribute, '用法不能为空');
                    } else if ($this->frequency[$key] == 0) {
                        $this->addError($attribute, '用药频次不能为空');
                    } else if($this->scenario != 'makeup' && $this->skin_test_status[$key] === '0' ){
                        $this->addError($attribute, '皮试不能为空');
                    }else if ($this->scenario != 'makeup' && $this->skin_test_status[$key] == 1 && empty($this->curelist_id[$key])) {
                        $this->addError($attribute, '皮试类型不能为空');
                    }
                    $recipe_id_key = isset($list['recipe_id']) ? $list['recipe_id'] : $list['id'];
                    $rows[$recipe_id_key][] = $this->type[$key] == 1 ? $this->num[$key] : 0;
                    if ($this->type[$key] == 1) {
                        $totalNum[$recipe_id_key] = $this->totalNum[$key];
                    }
//                    if (isset($list['recipe_id'])) {
//                        $rows[$list['recipe_id']][] = $this->type[$key] == 1 ? $this->num[$key] : 0;
//                        $totalNum[$list['recipe_id']] = $this->totalNum[$key];
//                    }
                }
                if (isset($list['isNewRecord']) && 0 == $list['isNewRecord']) {//删除项 修改项
                    $alreadyChargeId[] = $list['id'];
                }
            }

            if ($this->scenario != 'makeup') {
                $query = new Query();
                $query->from(['a' => RecipeRecord::tableName()]);
                $query->select(['b.id']);
                $query->leftJoin(['b' => ChargeInfo::tableName()], '{{a}}.id = {{b}}.outpatient_id');
                $query->where(['a.spot_id' => $this->spotId, 'b.status' => [1, 2], 'a.id' => $alreadyChargeId, 'b.type' => 4]);
                $result = $query->all();
                if ($result) {
                    $this->addError($attribute, '存在已经收费或退费的项目');
                }
            }

            if (!empty($totalNum)) {
                foreach ($totalNum as $key => $v) {
                    if ($v < array_sum($rows[$key])) {
                        $this->addError($attribute, '数量不能大于总库存量');
                    }
                }
            }
        }
    }

    public function validateDescription($attribute, $params) {
        if (count($this->description)) {
            if (is_array($this->description)) {
                foreach ($this->description as $key => $v) {
                    if ($this->deleted[$key] != 1) {
                        if (mb_strlen($v,'UTF-8') > 35) {
                            $this->addError('recipe_id', '描述说明不能超过35个字符');
                        }
                    }
                }
            }
        }
    }


    /**
     * @return 外购
     */
    public static function recipeOut($list, $model, $key, $hasRecord = null) {
        $db = Yii::$app->db;
        $recipeList = [];
        $recipeWhere = ['id' => $list['id'], 'spot_id' => self::$staticSpotId, 'record_id' => $model->record_id];
        if (isset($list['isNewRecord']) && $list['isNewRecord'] === 0) {
            //若为删除操作 deleted == 1 , 修改操作deleted == ''
            if ($model->deleted[$key] == 1) {
                //删除相应的处方检查信息记录
                $recipeRecordInfo = RecipeRecord::find()->select(['id', 'name', 'type', 'num', 'price', 'cure_id', 'curelist_id'])->where($recipeWhere)->asArray()->one();
                $cureRecord = CureRecord::find()->select(['status'])->where(['id' => $recipeRecordInfo['cure_id'], 'spot_id' => self::$staticSpotId])->asArray()->one();
                $chargeInfo = ChargeInfo::find()->select(['status'])->where(['outpatient_id' => $recipeRecordInfo['cure_id'], 'type' => ChargeInfo::$cureType])->asArray()->one();
                if ($cureRecord['status'] == 3 && (empty($chargeInfo) || $chargeInfo['status'] == 0)) {
                    $db->createCommand()->delete(CureRecord::tableName(), ['id' => $recipeRecordInfo['cure_id'], 'spot_id' => self::$staticSpotId, 'record_id' => $model->record_id])->execute();
                    $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $recipeRecordInfo['cure_id'], 'spot_id' => self::$staticSpotId, 'type' => ChargeInfo::$cureType])->execute();
                }
                $db->createCommand()->delete(RecipeRecord::tableName(), ['id' => $list['id'], 'spot_id' => self::$staticSpotId, 'record_id' => $model->record_id])->execute();
            } else {
                //修改对应的记录
                $time = RecipeRecord::find()->select(['num', 'curelist_id', 'cure_id'])->where($recipeWhere)->asArray()->one();
                $diffCount = intval($model->num[$key]) - intval($time['num']);

                $cureListId = $time['curelist_id'];
                $cureId = $time['cure_id'];
                if ($model->skin_test_status[$key] != 1 && $cureId != 0) {//不需要皮试，删除记录
                    $cureRecord = CureRecord::find()->select(['status'])->where(['id' => $cureId, 'spot_id' => self::$staticSpotId])->asArray()->one();
                    $chargeInfo = ChargeInfo::find()->select(['status'])->where(['outpatient_id' => $cureId, 'spot_id' => self::$staticSpotId, 'type' => ChargeInfo::$cureType])->asArray()->one();
                    if ($cureRecord['status'] == 3 && (empty($chargeInfo) || $chargeInfo['status'] == 0)) {
                        $db->createCommand()->delete(CureRecord::tableName(), ['id' => $cureId, 'spot_id' => self::$staticSpotId, 'record_id' => $model->record_id])->execute();
                        $db->createCommand()->delete(ChargeInfo::tableName(), ['outpatient_id' => $cureId, 'spot_id' => self::$staticSpotId, 'type' => ChargeInfo::$cureType])->execute();
                        $cureListId = 0;
                        $cureId = 0;
                    }
                } else if ($model->skin_test_status[$key] == 1 && $cureId == 0) {//若为不需要皮试转需要皮试
                    $cureListId = $model->curelist_id[$key];
                    $cureInfo = ClinicCure::getCure($cureListId);
                    $db->createCommand()->insert(CureRecord::tableName(), ['record_id' => $model->record_id, 'spot_id' => self::$staticSpotId, 'name' => $cureInfo['name'], 'unit' => $cureInfo['unit'], 'price' => $cureInfo['price'], 'time' => 1, 'tag_id' => $cureInfo['tag_id'], 'type' => 1, 'create_time' => time(), 'update_time' => time()])->execute();
                    $cureId = $db->lastInsertID;
                    if ($hasRecord['status'] == 5) {
                        $chargeRecordId = ChargeRecord::find()->select(['id'])->where(['spot_id' => self::$staticSpotId, 'record_id' => $model->record_id])->asArray()->one()['id'];
                        $reportInfo = \app\modules\report\models\Report::reportRecord($model->record_id);
                        $db->createCommand()->insert(ChargeInfo::tableName(), ['charge_record_id' => $chargeRecordId, 'spot_id' => self::$staticSpotId, 'record_id' => $model->record_id, 'type' => ChargeInfo::$cureType, 'outpatient_id' => $cureId, 'name' => $cureInfo['name'], 'unit' => $cureInfo['unit'], 'unit_price' => $cureInfo['price'], 'num' => 1, 'doctor_id' => $reportInfo['doctor_id'], 'update_time' => time(), 'tag_id' => $cureInfo['tag_id']])->execute(); //新增收费项
                    }
                }

                $db->createCommand()->update(RecipeRecord::tableName(), ['dose' => $model->dose[$key], 'used' => $model->used[$key], 'frequency' => $model->frequency[$key], 'day' => $model->day[$key], 'num' => $model->num[$key], 'type' => $model->type[$key], 'description' => $model->description[$key], 'dose_unit' => $model->dose_unit[$key], 'skin_test_status' => $model->skin_test_status[$key] ? $model->skin_test_status[$key] : 0, 'curelist_id' => $cureListId, 'cure_id' => $cureId], $recipeWhere)->execute();
            }
        } else {
            //新记录,delete === 0或者空 为新增
            if (!$model->deleted[$key]) {
                $cureListId = 0;
                $cureId = 0;
                if ($model->skin_test_status[$key] == 1) {//需要皮试，插入皮试记录
                    $cureListId = $model->curelist_id[$key];
                    $cureInfo = ClinicCure::getCure($cureListId);
                    $db->createCommand()->insert(CureRecord::tableName(), ['record_id' => $model->record_id, 'spot_id' => self::$staticSpotId, 'name' => $cureInfo['name'], 'unit' => $cureInfo['unit'], 'price' => $cureInfo['price'], 'time' => 1, 'tag_id' => $cureInfo['tag_id'], 'type' => 1, 'create_time' => time(), 'update_time' => time()])->execute();
                    $cureId = $db->lastInsertID;
                    if ($hasRecord['status'] == 5) {
                        $chargeRecordId = ChargeRecord::find()->select(['id'])->where(['spot_id' => self::$staticSpotId, 'record_id' => $model->record_id])->asArray()->one()['id'];
                        $reportInfo = \app\modules\report\models\Report::reportRecord($model->record_id);
                        $db->createCommand()->insert(ChargeInfo::tableName(), ['charge_record_id' => $chargeRecordId, 'spot_id' => self::$staticSpotId, 'record_id' => $model->record_id, 'type' => ChargeInfo::$cureType, 'outpatient_id' => $cureId, 'name' => $cureInfo['name'], 'unit' => $cureInfo['unit'], 'unit_price' => $cureInfo['price'], 'num' => 1, 'doctor_id' => $reportInfo['doctor_id'], 'update_time' => time(), 'tag_id' => $cureInfo['tag_id']])->execute(); //新增收费项
                    }
                }
                $recipeList[] = [$list['recipelist_id'], $model->record_id, self::$staticSpotId, $list['name'],$list['product_name'],$list['en_name'], $list['specification'], $list['unit'], $list['price'], $model->dose[$key], $list['type'], $model->used[$key], $model->day[$key], $model->frequency[$key], $model->num[$key], $model->type[$key], $model->description[$key], $model->dose_unit[$key], $list['medicine_description_id'], $model->skin_test_status[$key] ? $model->skin_test_status[$key] : 0 , $list['skin_test'], time(), time(), $cureListId, $cureId,$list['high_risk'],$list['remark'],$list['drug_type']];
            }
        }
        if (count($recipeList) > 0) {
            $db->createCommand()->batchInsert(RecipeRecord::tableName(), ['recipe_id', 'record_id', 'spot_id', 'name','product_name','en_name', 'specification', 'unit', 'price', 'dose', 'dosage_form', 'used', 'day', 'frequency', 'num', 'type', 'description', 'dose_unit', 'medicine_description_id', 'skin_test_status', 'skin_test', 'create_time', 'update_time', 'curelist_id', 'cure_id','high_risk','remark','drug_type'], $recipeList)->execute();
        }
    }

    /**
     * @param id 流水ID
     * @return 获取已经添加的处方医嘱
     */
    public static function getRecipeRecordDataProvider($id) {
        $query = new Query();
        $query->from(['a' => RecipeRecord::tableName()]);
        $query->select(['DISTINCT(a.id)', 'a.recipe_id', 'a.name', 'a.specification', 'a.dosage_form', 'a.unit', 'a.price', 'a.dose', 'a.used', 'a.frequency', 'a.day', 'a.num', 'a.description', 'a.type', 'a.status', 'a.remark', 'a.skin_test_status', 'a.skin_test', 'c.specification', 'a.dose_unit as r_dose_unit', 'c.dose_unit as l_dose_unit']);
        $query->leftJoin(['c' => RecipeList::tableName()], '{{a}}.recipe_id = {{c}}.id');
        $query->where(['a.record_id' => $id, 'a.spot_id' => self::$staticSpotId]);
//        if (ChargeInfo::getChargeRecordNum($id)) {
//            $query->addSelect(['charge_status' => 'b.status']);
//            $query->leftJoin(['b' => ChargeInfo::tableName()], '{{a}}.id = {{b}}.outpatient_id');
//            $query->andWhere([
//                'or',
//                ['b.type' => ChargeInfo::$recipeType],
//                ['a.type' => 2]
//            ]);
//        }
        $recipeRecordDataProvider = $query->all();
        foreach ($recipeRecordDataProvider as $key => $val) {
            $l_dose_unit = explode(',', $recipeRecordDataProvider[$key]['l_dose_unit']);
            $unit_num = count($l_dose_unit);
            $all_dose_unit = [];
            if ($unit_num != 1) {
                $all_dose_unit[''] = '';
            }
            foreach ($l_dose_unit as $val) {
                $all_dose_unit[$val] = RecipeList::$getDoseUnit[$val];
            }
            $recipeRecordDataProvider[$key]['l_dose_unit'] = $all_dose_unit; //剂量单位(可编辑状态)
        }
        return $recipeRecordDataProvider;
    }

    /**
     * @param $id 流水id
     * @return 处方记录id
     */
    public static function getRecipeRecord($id) {
        $recipeRecord = RecipeRecord::find()->select(['name', 'dose', 'dosage_form', 'num', 'unit', 'used', 'frequency', 'day', 'description', 'dose_unit', 'specification', 'skin_test_status', 'skin_test', 'record_id'])->where(['record_id' => $id])->asArray()->all();
        $data = [];
        foreach ($id as $val) {
            foreach ($recipeRecord as $v) {
                if ($val == $v['record_id']) {
                    $data[$v['record_id']][] = $v;
                }
            }
        }
        return $data;
    }

    public static function getRecipeRecordDetail($id) {
        $query = new Query();
        $query->from(['a' => RecipeRecord::tableName()]);
        $query->select(['a.record_id', 'a.name', 'a.dose', 'a.dosage_form', 'a.num', 'a.unit', 'a.used', 'a.frequency', 'a.day', 'a.description', 'a.dose_unit', 'a.specification', 'a.skin_test_status', 'a.skin_test', 'a.remark', 'b.cure_result as cureResult', 'b.name as cureName', 'a.high_risk']);
        $query->leftJoin(['b' => CureRecord::tableName()], '{{a}}.cure_id = {{b}}.id');
        $query->leftJoin(['c' => RecipeList::tableName()],'{{a}}.recipe_id = {{c}}.id' );
        $query->where(['a.record_id' => $id]);
        $query->andWhere('a.status <> 5');
        $recipeRecord = $query->all();
        $data = [];
        if (!empty($id) && is_array($id)) {
            foreach ($id as $val) {
                foreach ($recipeRecord as $v) {
                    if ($val == $v['record_id']) {
                        $data[$v['record_id']][] = $v;
                        $data[$v['record_id']]['name'] .= $v['name'] . ',';
                    }
                }
            }
        }
        return $data;
    }
    
    public static function getExecuteStatusOptions($status){
        if($status == 1){
            $color = 'blue';
            $title = '已发药';
        }else if($status == 3){
            $color = 'red';
            $title = '待发药';
        }else if($status == 4){
            $color = 'red';
            $title = '待退药';
        }else{
            $color = 'blue';
            $title = '已退药';
        }
        $options = [
            'class' => 'fa fa-flag margin-right '. $color,
            'title' => $title,
            'aria-label' => $title,
            'data-toggle' => 'tooltip'
        ];
        return $options;
    }


    /**
     * @return 返回该就诊记录的处方记录信息
     * @param 就诊流水id $id
     * @param 包含的处方id $recipeIds
     */
    public static  function findRecipeRecordPrintDataProvider($id, $recipeIds = null,$filterType = null) {
        $query = new Query();
        $query->from(['a' => RecipeRecord::tableName()]);

        $query->select(['DISTINCT(a.id)', 'a.recipe_id','a.product_name', 'a.name', 'a.unit', 'a.medicine_description_id', 'a.price', 'a.dosage_form', 'a.dose', 'a.used', 'a.frequency', 'a.day', 'a.num', 'a.description', 'a.type', 'a.status', 'a.specification', 'a.drug_type' ,'a.skin_test_status', 'a.skin_test', 'a.dose_unit as r_dose_unit', 'a.curelist_id', 'c.dose_unit as l_dose_unit', 'c.manufactor', 'a.remark', 'd.cure_result as cureResult', 'd.name as cureName', 'd.status as cureStatus', 'e.status as cureChargeStatus']);

        $query->leftJoin(['c' => RecipeList::tableName()], '{{a}}.recipe_id = {{c}}.id');
        $query->leftJoin(['d' => CureRecord::tableName()], '{{a}}.cure_id = {{d}}.id');
        $query->leftJoin(['e' => ChargeInfo::tableName()], '{{a}}.cure_id = {{e}}.outpatient_id AND {{e}}.type = 3');
        $query->where(['a.record_id' => $id, 'a.spot_id' => self::$staticSpotId]);
        $query->andFilterWhere(['a.id' => $recipeIds]);
        if($filterType == 1){ //精神类药物
          $query->andFilterWhere(['a.drug_type' => '20']);    
        }
        if($filterType == 2){ //其他药物
          $query->andFilterWhere(['!=','a.drug_type','20']);
        }
//         if (ChargeInfo::getChargeRecordNum($id)) {
//             $query->addSelect(['charge_status' => 'b.status']);
//             $query->leftJoin(['b' => ChargeInfo::tableName()], '{{a}}.id = {{b}}.outpatient_id');
//             $query->andWhere([
//                 'or',
//                 ['b.type' => ChargeInfo::$recipeType],
//                 ['a.type' => 2]
//             ]);
//         }


        $result = $query->all();
        foreach ($result as &$v) {
            $v['displayName'] = empty($v['specification']) ? $v['name'] : $v['name'] . '(' . $v[specification] . ')';
        }

        return $result;
    }

    /**
     *
     * @param 执行状态 $status
     * @param type 0-医生门诊，1-护士工作台
     * @return multitype:string 返回class数组样式
     */
    public static function getStatusOptions($status,$type = 0){
        if($status == 1){
            $color = 'blue';
            $title = '已发药';
        }else if($status == 3){
            $color = 'red';
            $title = '待发药';
        }else if($status == 4){
            $color = 'red';
            $title = '待退药';
        }else{
            $color = 'blue';
            $title = '已退药';
        }
        $options = [
            'class' => 'fa fa-flag margin-right '. $color,
            'title' => $title,
            'aria-label' => $title,
            'data-toggle' => 'tooltip'
        ];
        if(!$type){
            return $options;
        }else{
            $html = Html::tag('i','',$options);
            if($status == 1){
                $html .= '已发药';
            }else if($status == 3){
                $html .= '待发药';
            }else if($status == 4){
                $html .= '待退药';
            }else{
                $html .= '已退药';
            }
            return $html;
        }
    }

	 /**
     * 
     * @param 执行状态 $status
     * @return string 根据状态返回颜色
     */
    public static function getStatusColor($status){
        if($status == 3 || $status == 4){// 待发药／待退药——未完成状态
            $color = '#FF5000';//红色
        }else if($status == 1 || $status ==5){//已发药／已退药——完成状态
            $color = '#76A6EF';//蓝色
        }
        return $color;
    }

}
