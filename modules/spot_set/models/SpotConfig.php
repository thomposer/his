<?php

namespace app\modules\spot_set\models;

use Yii;

/**
 * This is the model class for table "{{%spot_config}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $begin_time
 * @property string $end_time
 * @property string $reservation_during
 * @property string $first_visit
 * @property string $return_visit
 * @property string $adult_check
 * @property string $child_check
 * @property string $create_time
 * @property string $update_time
 * @property string $logo_img
 * @property string $spot_name
 * @property integer $pub_tel
 * @property integer $label_tel
 *
 *
 */
class SpotConfig extends \app\common\base\BaseActiveRecord
{

    public $appointment_rebate;
    public $charge_rebate;
    public $inspect_rebate;
    public $check_rebate;
    public $cure_rebate;
    public $reception_rebate;
    public $childCare; //儿保

    /**
     * @inheritdoc
     */

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    public static function tableName() {
        return '{{%spot_config}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'begin_time', 'end_time', 'reservation_during'], 'required'],
            [['spot_id', 'reservation_during', 'first_visit', 'return_visit', 'adult_check', 'child_check', 'massage', 'create_time', 'update_time', 'childCare'], 'integer'],
//             ['end_time','compare', 'operator'=>'>', 'compareAttribute'=>'begin_time'],
            [['begin_time', 'end_time'], 'match', 'pattern' => '/0$/', 'message' => '暂不支持非10分钟倍数的时间设置.'],
            ['begin_time', 'validateBeginTime'],
            ['end_time', 'validateEndTime'],
            ['spot_name', 'string', 'max' => 20],
            [['pub_tel', 'label_tel'], 'integer'],
            [['pub_tel', 'label_tel'], 'string', 'max' => 32],
            ['logo_img', 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID',
            'begin_time' => '预约开始时间',
            'end_time' => '预约结束时间',
            'reservation_during' => '日历显示间隔',
            'first_visit' => '初诊(分钟)',
            'return_visit' => '复诊(分钟)',
            'massage' => '小儿推拿(分钟)',
            'adult_check' => '成人体检(分钟)',
            'child_check' => '儿童体检(分钟)',
            'create_time' => '创建时间',
            'update_time' => '结束时间',
            'appointment_rebate' => '病历打印',
            'charge_rebate' => '收费单打印',
            'check_rebate' => '检查报告打印',
            'cure_rebate' => '治疗单打印',
            'reception_rebate' => '处方打印',
            'inspect_rebate' => '检验报告打印',
            'logo_img' => '打印logo',
            'spot_name' => '注册诊所名称',
            'pub_tel' => '通用电话',
            'label_tel' => '药品标签打印电话',
        ];
    }

    /**
     * 
     * @var 时间配置
     */
    public static $getTimeConfig = [

        10 => 10,
        20 => 20,
        30 => 30,
        40 => 40,
        50 => 50,
        60 => 60,
        120 => 120,
    ];

    /**
     * @var 打印配置
     */
    public static $getrebatetype = [
        1 => 'A4样式',
        2 => 'A5样式',
    ];

    /**
     * @param  array | string $fields 查询字段
     * @return array 诊所的参数
     */
    public static function getConfig($fields = '*') {
        $config = self::find()->select($fields)->where(['spot_id' => self::$staticSpotId])->asArray()->one();
        return $config;
    }

    public function validateBeginTime($attribute) {
        $beginTime = $this->$attribute;
        $lastNum = substr($beginTime, -1);
        if ($lastNum != 0) {
            $this->addError($attribute, "暂不支持非10分钟倍数的时间设置");
        }
    }

    public function validateEndTime($attribute) {
        $beginTime = $this->$attribute;
        $lastNum = substr($beginTime, -1);
        $beginTime = date('Y-m-d') . ' ' . $this->begin_time;
        $endTime = date('Y-m-d') . ' ' . $this->end_time;
        if ($lastNum != 0) {
            $this->addError($attribute, "暂不支持非10分钟倍数的时间设置");
        } else if (strtotime($endTime) < strtotime($beginTime)) {
            $this->addError($attribute, '预约结束时间的值必须大于"预约开始时间"。');
        }
    }

    /**
     * 
     * @return type 获取诊所参数的开始结束 预约时间
     */
    public static function getAppointmentTimeConfig() {
        $spotAppointmentTime = SpotConfig::getConfig();
        $timeConfig = [
            'begin_time' => 0,
            'end_time' => 0
        ];
        if ($spotAppointmentTime && $spotAppointmentTime['begin_time']) {
            $timeConfig = [
                'begin_time' => $spotAppointmentTime['begin_time'],
                'end_time' => $spotAppointmentTime['end_time']
            ];
        }
        return $timeConfig;
    }

    /**
     * 
     * @return string 获取诊所  调查问卷的短信URL
     */
    public static function getMarketingSms() {
        if (YII_DEBUG) {//测试环境
            $res = [
                self::$staticSpotId => 'https://sojump.com/jq/15203839.aspx',
                75 => 'https://easyhin.wjx.cn/jq/19730081.aspx'//覆盖wangmiao诊所 便于测试
            ];
        } else {//正式环境
            $res = [
                59 => 'https://sojump.com/jq/15203839.aspx', //深圳海德
                62 => 'https://www.wjx.cn/jq/15203865.aspx',//上海丽晟
                64 =>'https://easyhin.wjx.cn/jq/19730081.aspx', //广州佑儿
            ];
        }
        return $res;
    }

}
