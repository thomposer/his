<?php

namespace app\modules\inspect\models;

use Yii;
use app\modules\outpatient\models\InspectRecord;
/**
 * This is the model class for table "{{%inspect_record_file}}".
 *
 * @property integer $id
 * @property integer $record_id
 * @property integer $spot_id
 * @property integer $inspect_record_id
 * @property string $file_url
 * @property integer $type
 * @property string $file_name
 * @property integer $size
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property InspectRecord $inspectRecord
 */
class InspectRecordFile extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%inspect_record_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['record_id', 'spot_id', 'inspect_record_id', 'type','file_name','size'], 'required'],
            [['record_id', 'spot_id', 'inspect_record_id', 'type', 'create_time', 'update_time'], 'integer'],
            [['file_url','file_name'], 'string', 'max' => 255],
            [['inspect_record_id'], 'exist', 'skipOnError' => true, 'targetClass' => InspectRecord::className(), 'targetAttribute' => ['inspect_record_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'record_id' => '就诊流水id',
            'spot_id' => '诊所id',
            'inspect_record_id' => '影像学就诊记录id',
            'file_url' => '文件路径',
            'type' => '文件类型(1-图片,2-文件)',
            'file_name' => '文件名',
            'size' => '文件大小',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInspectRecord()
    {
        return $this->hasOne(InspectRecord::className(), ['id' => 'inspect_record_id']);
    }
}
