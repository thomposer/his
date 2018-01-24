<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%child_examination_basic}}".
 * 儿童体检-基本信息
 * @property integer $id
 * @property integer $spot_id
 * @property integer $record_id
 * @property string $bregmatic
 * @property string $jaundice
 * @property integer $create_time
 * @property integer $update_time
 */
class ChildExaminationBasic extends \app\common\base\BaseActiveRecord
{
    public function init()
    {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%child_examination_basic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'record_id'], 'required'],
            [['spot_id', 'record_id', 'create_time', 'update_time'], 'integer'],
            [['bregmatic'], 'string', 'max' => 20],
//            ['bregmatic', 'match', 'pattern' => '/^([0-9]\d*(.[0-9]){0,1})$/', 'message' => '前囟只能精确到小数点后一位'],
            [['bregmatic', 'jaundice'], 'default', 'value' => ''],
            [['jaundice'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'spot_id' => '诊所id',
            'record_id' => '就诊流水id',
            'bregmatic' => '前囟',
            'jaundice' => '黄疸',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
