<?php

namespace app\modules\outpatient\models;

use Yii;
use yii\db\Exception;
use app\modules\patient\models\PatientRecord;
use yii\helpers\Json;

/**
 * This is the model class for table "{{%first_check}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $record_id
 * @property integer $check_code_id
 * @property string $content
 * @property integer $create_time
 * @property integer $update_time
 */
class FirstCheck extends \app\common\base\BaseActiveRecord
{

    public $check_id;
    public $deleted;
    public $check_code_type;

    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%first_check}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'record_id'], 'required'],
            [['spot_id', 'record_id', 'check_code_id', 'create_time', 'update_time'], 'integer'],
            [['content'], 'string', 'max' => 512],
            [['check_id', 'deleted'], 'safe'],
            [['check_degree'], 'default', 'value' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => 'Spot ID',
            'record_id' => 'Record ID',
            'check_code_id' => 'Check Code ID',
            'content' => 'Content',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * @desc 门诊保存病例时，保存初步诊断.
     * @return bool
     */
    public function outpatientSave($firstCheckData) {
        if (empty($this->record_id)) {
            return true;
        }

        $hasRecord = PatientRecord::find()->select(['id'])->where(['id' => $this->record_id, 'spot_id' => $this->spotId])->asArray()->one();
        if (!$hasRecord) {
            //不是本诊所的问诊单
            return false;
        }

        $success = true;
        $dbTrans = $this->getDb()->beginTransaction();
        try {
            //可以先删掉 历史数据
            FirstCheck::deleteAll(['spot_id' => $this->spotId, 'record_id' => $this->record_id]);
            $checkDegree=  array_values($firstCheckData['check_degree']);
            foreach ($firstCheckData['check_code_type'] as $key => $val) {
                if(2 == $val){//自定义为0
                    $firstCheckData['check_code_id'][$key] = 0;
                }
                $row[] = [$this->spotId, $this->record_id, $firstCheckData['check_code_id'][$key], $firstCheckData['content'][$key], $checkDegree[$key], time(), time()];
            }
            $this->getDb()->createCommand()->batchInsert(FirstCheck::tableName(), ['spot_id', 'record_id', 'check_code_id', 'content', 'check_degree', 'create_time', 'update_time'], $row)->execute();
//            foreach ($this->check_id as $key => $v){
//                $list = Json::decode($v);
//                //旧记录
//                if (isset($list['isNewRecord']) && $list['isNewRecord'] === 0) {
//                    //若为删除操作 deleted == 1
//                    if ($this->deleted[$key] == 1) {
//                        //删除相应的记录
//                        FirstCheck::deleteAll(['id' => $list['id'], 'spot_id' => $this->spotId, 'record_id' => $this->record_id]);
//                    }
//                } else {
//                    //新记录,delete === 0或者空 为新增
//                    if (!$this->deleted[$key]) {
//                        $insertData = new FirstCheck();
//                        $insertData->record_id = $this->record_id;
//                        $insertData->spot_id = $this->spot_id;
//                        $insertData->content = $list['content'];
//                        $insertData->check_code_id = $list['check_code_id'];
//                        $insertData->create_time = time();
//                        $insertData->update_time = time();
//                        if(!$insertData->save()){
//                            $dbTrans->rollBack();
//                            return false;
//                        }
//                    }
//                }
//            }
            $dbTrans->commit();
        } catch (Exception $e) {
            $dbTrans->rollBack();
            return false;
        }


        return $success;
    }

    /**
     * @desc 门诊保存病例时，判断初步诊断是否存在
     * @return bool
     */
    public function firstCheckRecord() {
        $success = false; //默认为空
        $data = $this->check_id;
        if (!$data) {
            return $success;
        }

        foreach ($data as $key => $v) {
            $list = Json::decode($v);
            if (isset($list['isNewRecord']) && $list['isNewRecord'] === 0) {//旧记录
                if ($this->deleted[$key] == 1) {
                    unset($data[$key]); //去掉删除项
                }
            } else {//存在新纪录,直接不为空
                return true;
            }
        }
        if ($data) {//旧记录没有全部删除
            $success = true;
        }
        return $success;
    }

    /**
     * @desc 获取当前诊所该就诊记录id的病历字段信息
     * @param integer $recordId 就诊流水id
     * @param array|string $fields 字段属性
     */
    public static function getFieldsList($recordId, $fields = '*') {

        return self::find()->select($fields)->where(['record_id' => $recordId, 'spot_id' => self::$staticSpotId])->asArray()->one();
    }

    /**
     * @desc 获取当前诊所的就诊记录id的初步诊断数量
     * @param integer $recordId 就诊流水id
     * @return \yii\db\ActiveRecord|NULL
     */
    public static function getCount($recordId) {
        return self::find()->where(['record_id' => $recordId, 'spot_id' => self::$staticSpotId])->count(1);
    }

    /**
     * @desc 获取多个问诊当初步诊断数据集.
     * @param $record_ids 就诊流水id集
     * @param $spot_id 诊所id
     * @return array 索引数组。record_id为索引.
     */
    public static function getPatientRecordFirstCheckInfo($record_ids, $spot_id = null) {
        $result = FirstCheck::find()
                ->select(['record_id', 'content', 'check_degree'])
                ->where(['record_id' => $record_ids])
                ->filterWhere(['spot_id' => $spot_id])
                ->orderBy(['id' => SORT_ASC])
                ->asArray()
                ->all();
        $row = [];
        if (!empty($result)) {
            foreach ($result as $key => $value) {
                $text = $value['check_degree'] == 2 ? $value['content'] . '?' : $value['content'];
                if (isset($row[$value['record_id']])) {
                    $row[$value['record_id']] = $row[$value['record_id']] . ',' . $text;
                } else {
                    $row[$value['record_id']] = $text;
                }
            }
        }
        return $row;
    }

    /**
     * @desc 获取某个问诊单初步诊断数据，.
     * @param $record_id 就诊流水id
     * @param $spot_id 诊所id
     * @return string 诊断代码.
     */
    public static function getFirstCheckInfo($record_id, $spot_id = null) {
        $data = FirstCheck::getPatientRecordFirstCheckInfo([$record_id], $spot_id);
        if (empty($data[$record_id])) {
            return '';
        } else {
            return $data[$record_id];
        }
    }

    public static $getCheckDegreeItems = [
        1 => '确诊',
        2 => '疑诊',
    ];

}
