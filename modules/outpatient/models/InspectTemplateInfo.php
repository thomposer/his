<?php

namespace app\modules\outpatient\models;

use Yii;
use app\modules\spot_set\models\InspectClinic;
use app\modules\spot\models\Inspect;
use yii\db\Query;
use app\modules\spot_set\models\InspectItemUnionClinic;

/**
 * This is the model class for table "{{%inspect_template_info}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $clinic_inspect_id
 * @property string $inspect_template_id
 * @property string $create_time
 * @property string $update_time
 *
 * @property InspectTemplate $inspectTemplate
 * @property InspectClinic $clinicInspect
 */
class InspectTemplateInfo extends \app\common\base\BaseActiveRecord
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
        return '{{%inspect_template_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'inspect_template_id'], 'integer'],
            ['clinic_inspect_id', 'validateInspectId'],
            [['deleted'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所id',
            'clinic_inspect_id' => '选择检验医嘱',
            'inspectName' => '选择检验医嘱',
            'inspect_template_id' => '检验模版配置id',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInspectTemplate() {
        return $this->hasOne(InspectTemplate::className(), ['id' => 'inspect_template_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClinicInspect() {
        return $this->hasOne(InspectClinic::className(), ['id' => 'clinic_inspect_id']);
    }

    public function validateInspectId($attribute, $params) {
        if (count($this->clinic_inspect_id) > 0) {
            $alreadyChargeId = array();
            foreach ($this->clinic_inspect_id as $key => $v) {
                try {
                    $list = json_decode($v, true);
                } catch (Exception $e) {
                    $this->addError($attribute, '参数错误');
                }
                if (!preg_match("/^\s*[+-]?\d+\s*$/", $list['id'])) {
                    $this->addError($attribute, '参数错误');
                }
            }
//            if (isset($list['isNewRecord']) && 0 == $list['isNewRecord'] && 1 == $this->deleted[$key]) {
//                $alreadyChargeId[] = $list['id'];
//            }
        }
    }

    public static function findInspectTemplateInfoDataProvider($id) {
        $data = (new Query())->from(['a' => InspectTemplateInfo::tableName()])
                        ->select(['a.id', 'name' => 'c.inspect_name', 'a.clinic_inspect_id'])
                        ->leftJoin(['b' => InspectClinic::tableName()], '{{a}}.clinic_inspect_id={{b}}.id')
                        ->leftJoin(['c' => Inspect::tableName()], '{{b}}.inspect_id={{c}}.id')
                        ->where(['a.inspect_template_id' => $id, 'a.spot_id' => self::$staticSpotId])
                        ->orderBy(['a.id' => SORT_ASC])->all();
        if (!empty($data)) {
            $clinicInspectIdArr = array_column($data, 'clinic_inspect_id');
            $clinicInspectItem = InspectItemUnionClinic::getInspectItemClinic($clinicInspectIdArr);
            foreach ($data as $key => &$value) {
                $value['inspectItem'] = isset($clinicInspectItem[$value['clinic_inspect_id']]) ? $clinicInspectItem[$value['clinic_inspect_id']] : [];
            }
        }
        return $data;
    }

    /**
     * 
     * @param type $model InspectTemplateModel
     * @param type $inspectTemplateInfoModel
     * @return 插入inspectTemplateInfoModel的关联信息
     */
    public static function saveInfo($model, $inspectTemplateInfoModel) {
        $result = $model->getModel('inspectTemplate')->save();
        if ($result) {
            $rows = [];
            foreach ($inspectTemplateInfoModel->clinic_inspect_id as $key => $v) {
                if ($inspectTemplateInfoModel->deleted[$key] == null) {
                    $info = json_decode($v, true);
                    $rows[] = [
                        self::$staticSpotId,
                        $info['id'],
                        $model->getModel('inspectTemplate')->id,
                        time(),
                        time()
                    ];
                }
            }
            if (count($rows) > 0) {
                Yii::$app->db->createCommand()->batchInsert(InspectTemplateInfo::tableName(), [
                    'spot_id', 'clinic_inspect_id', 'inspect_template_id', 'create_time', 'update_time'
                        ], $rows)->execute();
            }
            if (array_sum($inspectTemplateInfoModel->deleted) == count($inspectTemplateInfoModel->clinic_inspect_id)) {//全部是删除
                return false;
            } else {
                return true;
            }
        }
    }

    public static function updateInfo($model, $inspectTemplateInfoModel) {
        $result = $model->getModel('inspectTemplate')->save();
        if ($result) {
            $rows = [];
            $delNum = 0;
            $addNum = 0;
            foreach ($inspectTemplateInfoModel->clinic_inspect_id as $key => $v) {
                $list = json_decode($v, true);
                if (isset($list['isNewRecord']) && $list['isNewRecord'] === 0) {
                    if ($inspectTemplateInfoModel->deleted[$key] == 1) {
                        Yii::$app->db->createCommand()->delete(InspectTemplateInfo::tableName(), ['id' => $list['id'], 'spot_id' => self::$staticSpotId])->execute();
                    }
                } else {
                    if ($inspectTemplateInfoModel->deleted[$key] == null) {
                        $rows[] = [
                            self::$staticSpotId,
                            $list['id'],
                            $model->getModel('inspectTemplate')->id,
                            time(),
                            time()
                        ];
                    }
                }
            }
            if (count($rows) > 0) {
                Yii::$app->db->createCommand()->batchInsert(InspectTemplateInfo::tableName(), [
                    'spot_id', 'clinic_inspect_id', 'inspect_template_id', 'create_time', 'update_time'
                        ], $rows)->execute();
            }
            if (array_sum($inspectTemplateInfoModel->deleted) == count($inspectTemplateInfoModel->clinic_inspect_id)) {//全部是删除
                return false;
            } else {
                return true;
            }
        }
    }

}
