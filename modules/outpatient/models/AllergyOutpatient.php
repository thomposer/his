<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%allergy_outpatient}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $record_id
 * @property integer $type
 * @property string $allergy_content
 * @property integer $allergy_degree
 * @property string $create_time
 * @property string $update_time
 */
class AllergyOutpatient extends \app\common\base\BaseActiveRecord
{

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%allergy_outpatient}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['record_id', 'type', 'allergy_degree'], 'integer'],
            [['allergy_content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'record_id' => '就诊记录流水ID',
            'type' => '过敏类型[1/药物 2/食物 3/其他]',
            'allergy_content' => '过敏内容',
            'allergy_degree' => '过敏程度[1/确认过敏 2/疑似过敏]',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    public static $getAllergyType = [
        1 => '药物过敏',
        2 => '食物过敏',
        3 => '其它过敏'
    ];
    public static $getAllergyDegree = [
        0 => '',
        1 => '确认',
        2 => '疑似'
    ];
    public static $getAllergyDegreeItems = [
        1 => '确认过敏',
        2 => '疑似过敏',
    ];

    /**
     * 
     * @param type $recordId 就诊流水ID
     * @return 根据就诊ID获取其过敏史
     */
    public static function getAllergyByRecord($recordId) {
        $data = self::find()->select(['type', 'allergy_content', 'allergy_degree', 'record_id'])->where(['record_id' => $recordId, 'spot_id' => self::$staticSpotId])->asArray()->all();
        $res = [];
        $record = [];
        if (!empty($data)) {
            foreach ($data as $val) {
                $allergyDegree = $val['allergy_degree'] ? $val['allergy_degree'] : '';
                $res[$val['record_id']][$val['type']][] = $allergyDegree ? $val['allergy_content'] . '(' . self::$getAllergyDegree[$allergyDegree] . ')' : $val['allergy_content'];
            }
        }
        foreach ($res as $key => $value) {
            $tmp[1] = !empty($value[1]) ? implode('、', $value[1]) : '';
            $tmp[2] = !empty($value[2]) ? implode('、', $value[2]) : '';
            $tmp[3] = !empty($value[3]) ? implode('、', $value[3]) : '';
//            $tmp[0] = ($tmp[1] || $tmp[2]||$tmp[3]) ? 1 : 2;
            $record[$key] = $tmp;
        }
        return $record;
    }

    /**
     * 
     * @param type $id 就诊流水ID
     * @return type 获取过敏史的model
     */
    public static function findAllergyOutpatient($id) {
        $model = AllergyOutpatient::find()->where(['spot_id' => self::$staticSpotId, 'record_id' => $id])->all();
        if ($model !== null && !empty($model)) {
            return $model;
        } else {
            $model = new AllergyOutpatient();
            $model->record_id = $id;
            return [$model];
        }
    }

    /**
     * 
     * @param type $hasAllergy 是否有过敏史 1/无 2/有
     * @param type $allergyOutpatient 过敏史Array
     * @param type $recordId 就诊流水ID
     * @return boolean true 保存成功 false 保存失败
     */
    public static function saveAllergyOutpatient($hasAllergy, $allergyOutpatient, $recordId) {
        $result['errorCode'] = 0;
        $result['msg'] = '';
        $dbTrans = Yii::$app->db->beginTransaction();
        $db = Yii::$app->db;
        $db->createCommand()->delete(AllergyOutpatient::tableName(), ['record_id' => $recordId, 'spot_id' => self::$staticSpotId])->execute();
        if ($hasAllergy == 1) {//无过敏史
            $dbTrans->commit();
            return $result;
        }
        try {
            if(empty($allergyOutpatient['allergy_degree'])){
                $dbTrans->commit();
                return $result;
            }
            $allergyOutpatient['allergy_degree'] = $allergyOutpatient['allergy_degree'] ? array_values($allergyOutpatient['allergy_degree']) : [];
            foreach ($allergyOutpatient['type'] as $key => $val) {
                if ($allergyOutpatient['type'][$key] != '' && $allergyOutpatient['allergy_content'][$key] != '') {
                    $rows[] = [
                        'record_id' => $recordId,
                        'spot_id' => self::$staticSpotId,
                        'type' => $allergyOutpatient['type'][$key] ? $allergyOutpatient['type'][$key] : 0,
                        'allergy_content' => $allergyOutpatient['allergy_content'][$key],
                        'allergy_degree' => $allergyOutpatient['allergy_degree'][$key] ? $allergyOutpatient['allergy_degree'][$key] : 0,
                        'create_time' => time()
                    ];
                } else {
                    if($allergyOutpatient['type'][$key] == ''){
                        $result['errorCode'] = 1009;
                        $result['msg'] = '过敏类型不能为空';
                    }else if($allergyOutpatient['allergy_content'][$key] == ''){
                        $result['errorCode'] = 1009;
                        $result['msg'] = '名称不能为空';
                    }
                    $dbTrans->rollBack();
                    return $result;
                }
            }
            //批量插入记录
            $res = $rows ? $db->createCommand()->batchInsert(AllergyOutpatient::tableName(), ['record_id', 'spot_id', 'type', 'allergy_content', 'allergy_degree', 'create_time'], $rows)->execute() : '';
            $dbTrans->commit();
            return $result;
        } catch (Exception $e) {
            $dbTrans->rollBack();
            Yii::info('saveAllergyOutpatient failed ' . $e->getMessage());
            $result['errorCode'] = 1002;
            $result['msg'] = '保存失败';
            return $result;
        }
    }

}
