<?php

namespace app\modules\spot_set\models;

use Yii;
use app\common\base\BaseActiveRecord;

/**
 * This is the model class for table "{{%board}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $name
 * @property string $file_name
 * @property string $size
 * @property string $file_url
 * @property integer $type
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class Board extends BaseActiveRecord
{
    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%board}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id','name','status',], 'required'],
            ['name', 'default','value'=>''],
            [['spot_id', 'status', 'create_time', 'update_time','type'], 'integer'],
            [['name', 'file_name', 'file_url'], 'string', 'max' => 255],
            [['size'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '公告ID',
            'spot_id' => '诊所id',
            'name' => '公告名称',
            'file_name' => '文件名称',
            'size' => '文件大小',
            'file_url' => '文件路径',
            'type' => '文件类型(1-图片，2-文件)',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }


    /*
     * 状态
     */

    public static $getStatus = [
        1 => '正常',
        2 => '停用',
    ];
}
