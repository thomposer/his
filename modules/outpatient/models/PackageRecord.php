<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%package_record}}".
 *
 * @property string $id
 * @property string $record_id
 * @property string $template_id
 * @property string $spot_id
 * @property string $name
 * @property string $price
 * @property string $remarks
 * @property string $create_time
 * @property string $update_time
 */
class PackageRecord extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%package_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['record_id', 'template_id', 'spot_id'], 'required'],
            [['record_id', 'spot_id', 'create_time', 'update_time','template_id'], 'integer'],
            [['price'], 'number'],
            [['name'], 'string', 'max' => 64],
            [['remarks'],  'default', 'value' => ''],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'record_id' => '流水id',
            'template_id' => '模板id',
            'spot_id' => '诊所id',
            'name' => '模板名称',
            'price' => '诊疗费用',
            'remarks' => '诊金说明',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
