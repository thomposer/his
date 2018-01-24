<?php

namespace app\specialModules\recharge\models;

use Yii;
use app\common\Common;
use yii\helpers\Url;
use yii\db\Query;
use app\specialModules\recharge\models\CardServiceLeft;
use app\specialModules\recharge\models\CardSpotConfig;
use app\specialModules\recharge\models\ServiceConfig;
use app\modules\spot\models\CardManage;
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
    public $service_id;
    public $cardName;

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
            [['card_physical_id', 'parent_spot_id', 'update_time', 'create_time', 'id', 'card_type_code', 'card_type', 'f_effective_time'], 'integer'],
            [['card_id'], 'string', 'max' => 32],
            [['user_name'], 'string', 'max' => 50],
            [['phone'], 'match', 'pattern' => '/^\d{11}$/'],
            [['checkType', 'checkNum'], 'required', 'on' => 'check'],
            [['checkNum', 'user_name', 'phone'], 'trim'],
            [['user_name', 'phone'], 'required'],
            ['checkNum', 'validateNum', 'on' => 'check'],
            ['f_effective_time', 'validateTime', 'on' => 'create'],
            ['service_left', 'validateLeft', 'on' => 'create'],
        ];
    }

    public function scenarios() {
        $parent = parent::scenarios();
        $parent['check'] = ['checkType', 'checkNum', 'user_name', 'phone'];
        $parent['create'] = ['user_name', 'phone', 'card_id', 'card_physical_id', 'parent_spot_id', 'id', 'card_type_code', 'f_effective_time', 'service_total', 'service_left', 'card_type', 'service_id'];
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
            'f_card_desc' => '描述',
            'f_status' => '状态',
            'f_activate_time' => '激活时间',
            'f_invalid_time' => '失效时间',
            'checkType' => '类型',
            'checkNum' => '号码',
            'user_name' => '姓名',
            'phone' => '手机号',
            'cardName' => '卡名称'
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
            } else if ($record["f_status"] != 0) {
                $this->addError($attribute, '该卡已激活，您可在列表中查找');
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
            foreach ($this->service_left as $key => $value) {
                if ($value > $this->service_total[$key] || $value < 0) {
                    $this->addError($attribute, [$key, '剩余次数不能大于总次数,且不能为负']);
                    break;
                }
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
            $card = $cardInfo[$record['card_id']] ? $cardInfo[$record['card_id']] : [];
            $record = array_merge($record, $card);
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
        $cardInfo = Common::curlPost($activateCardUrl, ['card_type' => $card_type, 'f_card_id' => $f_card_id]);
        $cardInfo = json_decode($cardInfo, true);
        if ($cardInfo['res']) {
            return false;
        } else {
            return true;
        }
    }
    
    /*
     * 根据手机号获取用户所有服务卡信息
     */
    public static function getCardInfo($phone){
        $query = new Query();
        $data = $query->from(['a' => self::tableName()])
                ->select(['a.card_id', 'a.card_type_code', 'a.card_type', 'c.service_id',  'c.service_left', 'd.service_name'])
                ->leftJoin(['b' => CardSpotConfig::tableName()], '{{a}}.card_type_code={{b}}.card_type')
                ->leftJoin(['c' => CardServiceLeft::tableName()], '{{a}}.card_id={{c}}.card_id')
                ->leftJoin(['d' => ServiceConfig::tableName()], '{{c}}.service_id={{d}}.id')
                ->where(['a.phone' => $phone, 'b.spot_id' => self::$staticSpotId])
                ->andWhere(['>' , 'c.invalid_time', time()])
                ->all();
        if(empty($data)){
            return [];
        }
        $serviceDataList = [];
        $cardIdList = [];
        foreach ($data as $value) {
            $serviceDataList[$value['card_id']][$value['service_id']] = ['serviceId' => $value['service_id'], 'serviceName' => $value['service_name'], 'serviceLeft' =>$value['service_left']];
            if($value['service_left'] > 0){
                $cardIdList[$value['card_id']] = ['card_id' => $value['card_id'], 'card_type' => $value['card_type']];
            }
        }
        $getCardInfoUrl = Yii::$app->urlManager->createAbsoluteUrl([Yii::getAlias('@cardCenterCardInfoBySn')]);
        $cardInfoList = Common::curlPost($getCardInfoUrl, ['f_card_id' => array_values($cardIdList)]);
        $cardInfoList = json_decode($cardInfoList, true);
        $result = [];
        foreach ($cardIdList as $key => $value) {
            if(isset($cardInfoList[$key]) && $cardInfoList[$key]['f_status'] == 1){//存在且启用
                $result[$key]['cardId'] = $cardIdList[$key]['card_id'];
                $result[$key]['cardType'] = $cardIdList[$key]['card_type'];
                $result[$key]['typeCode'] = $cardInfoList[$key]['f_card_type_code'];
                $result[$key]['idCode'] = $cardInfoList[$key]['f_identifying_code'];
                $result[$key]['cardName'] = isset(CardManage::$cardTypeCode[$cardInfoList[$key]['f_card_type_code']]) ? CardManage::$cardTypeCode[$cardInfoList[$key]['f_card_type_code']] : '类型码：'.$cardInfoList[$key]['f_card_type_code'];
                $result[$key]['serviceList'] = $serviceDataList[$key];
            }
        }
        return $result;
    }

}
