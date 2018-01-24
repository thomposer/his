<?php

namespace app\modules\spot_set\models;

use Yii;
use app\common\base\BaseActiveRecord;

/**
 * This is the model class for table "{{%once_department}}".
 *
 * @property integer $id
 * @property string $name
 * @property integer $spot_id
 * @property integer $create_time
 * @property integer $update_time
 */
class OnceDepartment extends BaseActiveRecord
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
        return '{{%once_department}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','spot_id'], 'required'],
            [['create_time', 'update_time','spot_id'], 'integer'],
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
            'name' => '一级科室名',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
