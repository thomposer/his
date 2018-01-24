<?php

namespace app\specialModules\recharge\models;

use Yii;

/**
 * This is the model class for table "t_category_history".
 *
 * @property string $f_physical_id
 * @property string $f_record_id
 * @property string $f_beg_category
 * @property string $f_end_category
 * @property string $f_user_id
 * @property string $f_user_name
 * @property integer $f_state
 * @property string $f_create_time
 * @property string $f_update_time
 */
class CategoryHistory extends \yii\db\ActiveRecord
{

    public $beg_category_name;
    public $end_category_name;

    public function init() {
        parent::init();
        $this->f_spot_id = isset($_COOKIE['spotId']) ? $_COOKIE['spotId'] : 0;
    }

    public function behaviors() {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%category_history}}';
    }

    public static function getDb() {
        return Yii::$app->get('cardCenter');
    }

    public function beforeSave($insert) {
        //判断当前表中是否相应更新时间字段
        if ($insert) {
            if (!$this->f_user_name) {
                $userInfo = Yii::$app->user->identity;
                $this->f_user_id = $userInfo ? $userInfo->id : 0;
                $this->f_user_name = $userInfo ? $userInfo->username : '系统';
            }
            $this->f_create_time = time();
        }
        $this->f_update_time = time();
        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['f_end_category'], 'required'],
            [['f_record_id', 'f_beg_category', 'f_end_category', 'f_user_id', 'f_state', 'f_create_time', 'f_update_time'], 'integer'],
            [['f_user_name'], 'string', 'max' => 100],
            [['f_change_reason'], 'string', 'max' => 50],
            [['f_user_id'], 'default', 'value' => 0]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'f_physical_id' => 'F Physical ID',
            'f_record_id' => '卡ID',
            'f_beg_category' => '原卡种',
            'f_end_category' => '变更后卡种',
            'beg_category_name' => '原卡种',
            'end_category_name' => '变更后卡种',
            'f_user_id' => '操作人',
            'f_spot_id' => '来源渠道',
            'f_user_name' => '操作人名称',
            'f_state' => 'F State',
            'f_create_time' => '时间',
            'f_change_reason' => '变更原因',
            'f_update_time' => 'F Update Time',
        ];
    }

}
