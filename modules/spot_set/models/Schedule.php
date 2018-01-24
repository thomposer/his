<?php

namespace app\modules\spot_set\models;

use app\common\base\BaseActiveRecord;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%schedule}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $shift_name
 * @property string $shift_time
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class Schedule extends BaseActiveRecord
{

    public $shift_timef;
    public $shift_timet;

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%schedule}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['shift_name', 'shift_timef', 'shift_timet', 'status'], 'required'],
            [['shift_name'], 'string', 'max' => 30],
            [['shift_name'], 'validateShiftName'],
            [['shift_name'], 'trim'],
            [['shift_time'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'shift_name' => '班次名称',
            'shift_time' => '班次时间',
            'status' => '状态',
            'shift_timef' => '班次开始时间',
            'shift_timet' => '班次结束时间',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    public static $getStatus = [
        1 => '正常',
        2 => '停用',
    ];

    public function validateShiftName($attribute) {
        $spotId = $this->spotId;
        if ($this->isNewRecord) {
            $hasRecord = Schedule::find()->select(['id'])->where(['spot_id' => $spotId, $attribute => $this->$attribute])->asArray()->limit(1)->one();
            if ($hasRecord) {
                $this->addError($attribute, '该班次名称已存在');
            }
        } else {
            $oldShiftName = $this->getOldAttribute('shift_name');
            if ($oldShiftName != $this->shift_name) {
                $hasRecord = $this->checkDuplicate('shift_name', $this->shift_name);
                if ($hasRecord) {
                    $this->addError('shift_name', '该班次名称已存在');
                }
            }
        }
    }

    protected function checkDuplicate($attribute, $params) {
        $spotId = $this->spotId;
        $hasRecord = Schedule::find()->select(['id'])->where(['spot_id' => $spotId, $attribute => $params])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @return type 获取班次配置
     */
    public static function getSecheduleConf() {
        $schedule = Schedule::find()->select(['id', 'shift_name'])->where(['status' => 1, 'spot_id' => self::$staticSpotId])->asArray()->all();
        foreach ($schedule as &$val) {
            $val['shift_name'] = Html::encode($val['shift_name']);
        }
        return $schedule;
    }

}
