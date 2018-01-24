<?php

namespace app\modules\follow\models;

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
class FollowFile extends \app\common\base\BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%follow_file}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['follow_id', 'spot_id'], 'required'],
            [['spot_id', 'follow_id', 'type', 'create_time', 'update_time'], 'integer'],
            [['file_name', 'file_url'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => 'Spot ID',
            'file_name' => 'File Name',
            'size' => 'Size',
            'file_url' => 'File Url',
            'type' => 'Type',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

}
