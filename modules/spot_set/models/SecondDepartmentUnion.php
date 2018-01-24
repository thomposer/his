<?php

namespace app\modules\spot_set\models;

use Yii;

/**
 * This is the model class for table "{{%second_department_union}}".
 *
 * @property string $id
 * @property string $second_department_id
 * @property string $spot_id
 * @property string $create_time
 * @property string $update_time
 */
class SecondDepartmentUnion extends \app\common\base\BaseActiveRecord
{
    public function init(){

        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%second_department_union}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['update_time'],'default','value' => 0],
            [['second_department_id', 'spot_id', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'second_department_id' => '二级科室id',
            'spot_id' => '诊所id',
        ];
    }

    /**
     * @desc 获取诊所下选中的二级科室
     */
    public static function getSelectSecondDepartment(){
        $data = self::find()->select(['second_department_id'])->where(['spot_id' => self::$staticSpotId])->asArray()->all();
        return $data;
    }
}
