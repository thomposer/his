<?php

namespace app\modules\spot\models;

use Yii;

/**
 * This is the model class for table "{{%once_department}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $spot_id
 * @property integer $create_time
 * @property integer $update_time
 */
class OnceDepartment extends \app\common\base\BaseActiveRecord
{
    public function init() {
        parent::init();
        $this->spot_id = $this->parentSpotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%once_department}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['name', 'required'],
            [['spot_id', 'create_time', 'update_time'], 'integer'],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '科室名称',
            'spot_id' => '机构id',
            'status' => '状态',
            'room_type' => '科室性质',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
