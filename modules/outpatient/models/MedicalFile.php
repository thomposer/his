<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%medical_file}}".
 *
 * @property string $id
 * @property string $record_id
 * @property string $spot_id
 * @property string $file_name
 * @property string $size
 * @property string $file_url
 * @property integer $type
 * @property string $create_time
 * @property string $update_time
 */
class MedicalFile extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%medical_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['record_id', 'spot_id'], 'required'],
            [['record_id', 'spot_id', 'type', 'create_time', 'update_time'], 'integer'],
            [['file_name', 'file_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'record_id' => 'Record ID',
            'spot_id' => 'Spot ID',
            'file_name' => 'File Name',
            'size' => 'Size',
            'file_url' => 'File Url',
            'type' => 'Type',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
    
    /**
     * @desc 获取当前诊所的就诊记录id的上传附件数量
     * @param integer $recordId 就诊流水id
     * @return \yii\db\ActiveRecord|NULL
     */
    public static function getCount($recordId){
        return self::find()->where(['record_id' => $recordId,'spot_id' => self::$staticSpotId])->count(1);
    
    }
}
