<?php

namespace app\modules\spot_set\models;

use Yii;
use yii\db\Query;
use app\modules\spot_set\models\InspectClinic;
use app\modules\spot\models\Inspect;
use app\modules\spot_set\models\InspectItemUnionClinic;
use yii\db\Exception;
use yii\base\Object;

/**
 * This is the model class for table "{{%outpatient_package_inspect}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $outpatient_package_id
 * @property integer $inspect_id
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property InspectClinic $inspect
 * @property OutpatientPackageTemplate $outpatientPackage
 */
class OutpatientPackageInspect extends \app\common\base\BaseActiveRecord
{

    public $inspectName;
    public $deleted;

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%outpatient_package_inspect}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id'], 'required'],
            [['spot_id', 'outpatient_package_id', 'create_time', 'update_time'], 'integer'],
            [['inspect_id'], 'validateInspectId'],
            [['deleted', 'inspectName'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所id',
            'outpatient_package_id' => '医嘱模板套餐公共信息表id',
            'inspect_id' => '诊所检验医嘱的id',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'inspectName' => '实验室检查'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInspect() {
        return $this->hasOne(InspectClinic::className(), ['id' => 'inspect_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOutpatientPackage() {
        return $this->hasOne(OutpatientPackageTemplate::className(), ['id' => 'outpatient_package_id']);
    }

    public function validateInspectId($attribute, $params) {
        if (count($this->inspect_id) > 0) {
            foreach ($this->inspect_id as $key => $v) {

                if (!preg_match("/^\s*[+-]?\d+\s*$/", $v)) {
                    $this->addError($attribute, '参数错误');
                }
            }
        }
    }

    /*
     * 获取检验医嘱模板详情
     * @param $idArr 套餐id或id数组
     * 
     */

    public static function getInspectList($idArr) {
        $inspectListQuery = new Query();
        $inspectList = $inspectListQuery->from(['a' => self::tableName()])
                ->leftJoin(['b' => InspectClinic::tableName()], '{{a}}.inspect_id = {{b}}.id')
                ->leftJoin(['c' => Inspect::tableName()], '{{b}}.inspect_id = {{c}}.id')
                ->select(['a.outpatient_package_id', 'clinic_inspect_id' => 'b.id', 'name' => 'c.inspect_name', 'unit' => 'c.inspect_unit',

                    'price' => 'b.inspect_price', 'inspect_id' => 'c.id', 'c.tag_id', 'b.deliver', 'b.specimen_type','b.deliver_organization',

                    'b.cuvette', 'b.inspect_type', 'c.inspect_english_name', 'b.remark', 'b.description'])
                ->where(['a.spot_id' => self::$staticSpotId, 'a.outpatient_package_id' => $idArr, 'b.status' => 1])
                ->all();
        if (!empty($inspectList)) {
            $inspectClinicId = array_column($inspectList, 'clinic_inspect_id');
            $inspectItemClinicArr = InspectItemUnionClinic::getInspectItemClinic($inspectClinicId);
            foreach ($inspectList as &$val) {
                $val['inspectItem'] = isset($inspectItemClinicArr[$val['clinic_inspect_id']]) ? $inspectItemClinicArr[$val['clinic_inspect_id']] : [];
            }
        }
        return $inspectList;
    }

    /**
     * 
     * @param integer $templateId 模板套餐id
     * @param Object $model 保存信息
     * @param integer $isNewRecord 1-新增，2-编辑,默认为1 
     * @return boolean 保存新增／编辑的检验医嘱信息
     */
    public static function saveInfo($templateId, $model, $isNewRecord = 1) {
        $rows = [];
        if ($isNewRecord == 2) {
            OutpatientPackageInspect::deleteAll(['outpatient_package_id' => $templateId, 'spot_id' => self::$staticSpotId]);
        }
        if (count($model->inspect_id)) {
            foreach ($model->inspect_id as $key => $v) {
                $rows[] = [
                    self::$staticSpotId,
                    $v,
                    $templateId,
                    time(),
                    time()
                ];
            }
            if (count($rows) > 0) {
                Yii::$app->db->createCommand()->batchInsert(self::tableName(), [
                    'spot_id', 'inspect_id', 'outpatient_package_id', 'create_time', 'update_time'
                        ], $rows)->execute();
            }
        }
        return true;
    }

}
