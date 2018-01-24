<?php

namespace app\modules\patient\models;

use Yii;
use app\modules\outpatient\models\AllergyOutpatient;
use yii\db\Query;

/**
 * This is the model class for table "{{%patient_allergy}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $patient_id
 * @property integer $type
 * @property string $allergy_content
 * @property integer $allergy_degree
 * @property string $create_time
 * @property string $update_time
 */
class PatientAllergy extends \app\common\base\BaseActiveRecord
{

    public function init()
    {
        parent ::init();
        $this -> spot_id = $this -> parentSpotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%patient_allergy}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'patient_id', 'type', 'allergy_degree', 'create_time', 'update_time'], 'integer'],
            [['allergy_content'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '机构ID',
            'patient_id' => '患者ID',
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

    /**
     * @param type $patientId 患者ID
     * @param type $recordId 就诊流水ID
     * @return 同步就诊记录的过敏史到患者基本信息的过敏史
     */
    public static function syncPatientAllergy($patientId, $recordId)
    {
        //获取当前就诊记录的过敏史
        $allergy = AllergyOutpatient ::find() -> where(['record_id' => $recordId, 'spot_id' => self ::$staticSpotId]) -> asArray() -> all();
        //先删掉患者基本信息的过敏史
        PatientAllergy ::deleteAll(['patient_id' => $patientId, 'spot_id' => self ::$staticParentSpotId]);
        if (!empty($allergy)) {
            foreach ($allergy as $val) {
                $row[] = [self ::$staticParentSpotId, $patientId, $val['type'], $val['allergy_content'], $val['allergy_degree'], time(), time()];
            }
            //批量插入过敏史记录
            Yii ::$app -> db -> createCommand() -> batchInsert(PatientAllergy ::tableName(), ['spot_id', 'patient_id', 'type', 'allergy_content', 'allergy_degree', 'create_time', 'update_time'], $row) -> execute();
        }
    }

    /**
     * @param type $patientId 患者ID
     * @param type $recordId 就诊流水ID
     * @return 同步患者基本信息的过敏史到就诊记录的过敏史
     */
    public static function syncOutpatientAllergy($patientId, $recordId)
    {
        //获取患者基本信息过敏史
        $patientAllergy = self ::find() -> where(['patient_id' => $patientId, 'spot_id' => self ::$staticParentSpotId]) -> asArray() -> all();
        if (!empty($patientAllergy)) {
            foreach ($patientAllergy as $val) {
                $row[] = [
                    'spot_id' => self ::$staticSpotId,
                    'record_id' => $recordId,
                    'type' => $val['type'],
                    'allergy_content' => $val['allergy_content'],
                    'allergy_degree' => $val['allergy_degree'],
                    'create_time' => time(),
                    'update_time' => time(),
                ];
            }
            //批量插入过敏史记录
            Yii ::$app -> db -> createCommand() -> batchInsert(AllergyOutpatient ::tableName(), ['spot_id', 'record_id', 'type', 'allergy_content', 'allergy_degree', 'create_time', 'update_time'], $row) -> execute();
        }
    }

    /**
     * 根据病人id获取过敏史
     * @param $patientId 病人id
     * @return object 过敏史列表对象
     */
    public static function getPatientAllergy($patientId)
    {
        $model = PatientAllergy ::find() -> where(['spot_id' => self ::$staticParentSpotId, 'patient_id' => $patientId]) -> all();

        return $model;
    }

    /**
     * 根据病人id获取过敏史
     * @param $patientId 病人id
     * @return array 过敏史列表
     */
    public static function getPatientAllergyArray($patientId)
    {
        $result = (new Query())
            -> select(["type", "allergy_content", "allergy_degree"])
            -> from(self ::tableName())
            -> where(["patient_id" => $patientId, 'spot_id' => self ::$staticParentSpotId])
            -> all();

        $data = [];
        foreach ($result as $key => $value) {
            $state = $value["allergy_degree"];
            if ($state == 1) {
                $state = "(确认)";
            } else if ($state == 2) {
                $state = "(疑似)";
            } else {
                $state = "";
            }
            if (isset($data[$value["type"]])) {
                $data[$value["type"]] = $data[$value["type"]] . "、" . $value["allergy_content"] . $state;
            } else {
                $data[$value["type"]] = $value["allergy_content"] . $state;
            }
        }
        return $data;
    }
}