<?php

namespace app\modules\card\models;

use Yii;
use app\common\Common;
use yii\helpers\Url;

/**
 * This is the model class for table "{{%user_card}}".
 *
 * @property string $id
 * @property string $card_id
 * @property string $card_physical_id
 * @property string $user_name
 * @property string $phone
 * @property string $service_id
 * @property string $parent_spot_id
 * @property string $service_left
 * @property string $update_time
 * @property string $create_time
 */
class UserCard extends \app\common\base\BaseActiveRecord
{

    public $f_card_desc; //说明
    public $f_status; //状态
    public $f_activate_time; //激活时间
    public $f_invalid_time; //失效时间
    public $f_effective_time; //生效时间
    public $checkType = 1; //验证类型
    public $checkNum; //验证号码 
    public $service_left;
    public $service_total;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%user_card}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['card_physical_id', 'parent_spot_id', 'update_time', 'create_time', 'id', 'card_type_code','card_type', 'f_effective_time'], 'integer'],
            [['card_id'], 'string', 'max' => 32],
            [['user_name'], 'string', 'max' => 50],
            [['phone'], 'string', 'max' => 11],
            [['checkType', 'checkNum'], 'required', 'on' => 'check'],
            [['user_name', 'phone'], 'required'],
            ['checkNum', 'validateNum', 'on' => 'check'],
            ['f_effective_time', 'validateTime', 'on' => 'create'],
            ['service_left', 'validateLeft', 'on' => 'create'],
        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['check'] = ['checkType', 'checkNum'];
        $parent['create'] = ['user_name', 'phone', 'card_id', 'card_physical_id', 'parent_spot_id', 'id', 'card_type_code', 'f_effective_time', 'service_total', 'service_left','card_type'];
        return $parent;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'card_id' => '卡号',
            'card_physical_id' => '会员卡主键ID',
            'user_name' => '持卡人信息',
            'phone' => '手机号',
            'card_type' => '类型码',
            'card_type_code' => '类型码',
            'parent_spot_id' => '机构ID',
            'service_left' => '剩余次数',
            'update_time' => 'Update Time',
            'create_time' => 'Create Time',
            'f_card_desc' => '说明',
            'f_status' => '状态',
            'f_activate_time' => '激活时间',
            'f_invalid_time' => '失效时间',
            'checkType' => '类型',
            'checkNum' => '号码',
        ];
    }

    public static $getStatus = [
        '0' => '未激活',
        '1' => '正常',
        '2' => '停用'
    ];
    public static $chekcTypeItem = [
        '1' => '卡号',
//        '2' => '手机号'
    ];

    public function validateNum($attribute, $params) {
        if (!$this->hasErrors()) {
            $record = self::checkCard($this->checkType, $this->checkNum);
            if (empty($record)) {
                $this->addError($attribute, '未找到满足条件的卡片');
            }
            $spotRecord = CardSpotConfig::find()->where(['card_type' => $record['f_card_type_code']])->andWhere([
                        'or',
                        ['spot_id' => self::$staticSpotId],
                        ['parent_spot_id' => self::$staticParentSpotId]
                    ])->one();
            if (empty($spotRecord) || is_null($spotRecord)) {
                $this->addError($attribute, '该会员卡不适用于当前诊所，请联系客服确认');
            }
        }
    }

    public function validateTime($attribute, $params) {
        if (!$this->hasErrors()) {
            if ($this->f_effective_time >= time() || empty($this->f_effective_time)) {
                $this->addError($attribute, '该卡片暂未生效');
            }
        }
    }

    public function validateLeft($attribute, $params) {
        if (!$this->hasErrors()) {
            if ($this->service_left > $this->service_total || $this->service_left < 0) {
                $this->addError($attribute, '剩余次数不能大于总次数,且不能为负');
            }
        }
    }

    public static function checkCard($checkType, $checkNum) {
        //先检查本地数据
        if ($checkType == 1) {
            $where['card_id'] = $checkNum;
        } else {
            $where['phone'] = $checkNum;
        }
        $record = [];
        $record = Self::find()->where($where)->asArray()->one();
        $getCardInfoUrl = Yii::$app->request->getHostInfo() . Url::to(['@cardCenterCardInfoBySn']);
        if ($record) {
            //去卡中心拉取卡的信息
            $cardInfo = Common::curlPost($getCardInfoUrl, ['f_card_id' => [['card_id' => $record['card_id'], 'card_type' => $record['card_type']]]]);
            $cardInfo = json_decode($cardInfo, true);
            $record = array_merge($record, $cardInfo[$record['card_id']]);
            $record['dataFrom'] = 1;
        } else {//去卡中心查询是否有这条数据
            if ($checkType == 1) {
                $cardCenterUrl = Yii::$app->request->getHostInfo() . Url::to(['@cardCenterCardInfoBySnOne']);
                $cardInfo = Common::curlPost($cardCenterUrl, ['f_card_id' => $checkNum]);
//                $cardInfo = Common::curlPost($getCardInfoUrl, ['f_card_id' => [['card_id' => $checkNum, 'card_type' => $checkNum]]]);
                if (!empty($cardInfo)) {
                    $cardInfo = json_decode($cardInfo, true);
                    $record = $cardInfo;
                    $record['dataFrom'] = 2;
                }
            }
        }
        return $record;
    }

    /**
     * 
     * @param 卡sn $f_card_id
     * @return 激活卡片 Description
     */
    public static function activateCard($f_card_id, $card_type = 1) {
        $activateCardUrl = Yii::$app->request->getHostInfo() . Url::to(['@cardCenterActivateCard']);
        $cardInfo = Common::curlPost($activateCardUrl, ['card_type' => $card_type,'f_card_id' => $f_card_id]);
        $cardInfo = json_decode($cardInfo, true);
        if ($cardInfo['res']) {
            return false;
        } else {
            return true;
        }
    }

}
