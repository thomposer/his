<?php

namespace app\modules\triage\models;

use app\modules\spot\models\NursingRecordTemplate;
use Yii;

/**
 * This is the model class for table "{{%nursing_record}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $creater_id
 * @property string $executor
 * @property string $name
 * @property string $content
 * @property string $execute_time
 * @property string $create_time
 * @property string $update_time
 * @property string $template_id
 */
class NursingRecord extends \app\common\base\BaseActiveRecord
{

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%nursing_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'record_id', 'creater_id', 'template_id', 'create_time', 'update_time'], 'integer'],
            [['executor', 'content', 'template_id', 'execute_time'], 'required'],
            [['name', 'execute_time'], 'string'],
            [['executor'], 'string', 'max' => 10],
            [['content'], 'string', 'max' => 1000],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '机构ID',
            'record_id' => '流水ID',
            'creater_id' => '创建人ID',
            'template_id' => '护理项',
            'executor' => '执行人',
            'name' => '护理项',
            'content' => '内容',
            'execute_time' => '执行时间',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->creater_id = $this->userInfo->id;  // 创建人
        }
        $this->execute_time = strtotime($this->execute_time);
        $content = NursingRecordTemplate::getNursingRecordTemplate(['id' => $this->template_id])[0]['name'];
        $this->name = $content ? $content : '';
        $this->record_id = Yii::$app->request->get('recordId');
        return parent::beforeSave($insert);
    }

    /**
     * @param $id 流水id
     * @return 护理记录
     */
    public static function getNurseRecord($id) {
        $nurseRecord = NursingRecord::find()->select(['name', 'executor', 'content', 'execute_time', 'record_id'])->where(['record_id' => $id])->asArray()->all();
        $data = [];
        if (!empty($id) && is_array($id)) {
            foreach ($id as $val) {
                foreach ($nurseRecord as $v) {
                    if ($val == $v['record_id']) {
                        $data[$v['record_id']][] = $v;
                    }
                }
            }
        }
        return $data;
    }

}
