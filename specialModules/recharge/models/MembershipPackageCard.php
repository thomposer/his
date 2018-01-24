<?php

namespace app\specialModules\recharge\models;

use Yii;
use yii\db\Query;
use app\modules\spot\models\PackageServiceUnion;
use app\specialModules\recharge\models\MembershipPackageCardUnion;
use app\specialModules\recharge\models\MembershipPackageCardService;
use app\modules\spot\models\PackageCard;
use app\modules\spot\models\PackageCardService;
use app\modules\patient\models\Patient;
use app\modules\spot\models\Spot;
use app\common\Common;
use DateTime;

/**
 * This is the model class for table "{{%membership_package_card}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $package_card_id
 * @property integer $status
 * @property string $remark
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property MembershipPackageCardUnion[] $membershipPackageCardUnions
 */
class MembershipPackageCard extends \app\common\base\BaseActiveRecord
{

    public $buyTime; //购卡时间
    public $price; //价格
    public $validityTime; //有效期
    public $content; //套餐内容
    public $username; //患者名称
    public $sex; //患者性别
    public $birthday; //患者出生日期
    public $iphone; //患者手机号
    public $name; //套餐卡名称
    public $validity_period; //过期时间
    public $active_time; //激活时间
    public $patientInfo; //患者信息
    public $patient_id; //患者id

    public function init() {

        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%membership_package_card}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'package_card_id', 'status'], 'required'],
            [['spot_id', 'package_card_id', 'status', 'create_time', 'update_time','patient_id'], 'integer'],
            [['remark'], 'string', 'max' => 255],
            [['remark'], 'default', 'value' => ''],
            [['package_card_id'], 'validateCardId'],
            [['buyTime', 'price', 'validityTime', 'content'], 'safe']
        ];
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['update'] = ['status', 'remark'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所id',
            'package_card_id' => '套餐名称',
            'status' => '状态',
            'remark' => '备注',
//            'baby_info' => '宝宝信息',
            'patientInfo' => '宝宝信息',
            'username' => '宝宝姓名',
            'iphone' => '手机号',
            'name' => '名称',
            'active_time' => '激活时间',
            'validity_period' => '失效时间',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'buyTime' => '购买时间',
            'price' => '价格',
            'validityTime' => '有效期（年）',
            'content' => '套餐内容'
        ];
    }

    public static $cardStatus = [
        1 => '正常',
        2 => '停用',
    ];

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMembershipPackageCardUnions() {
        return $this->hasMany(MembershipPackageCardUnion::className(), ['membership_package_card_id' => 'id']);
    }

    /**
     * @desc 返回某条记录的信息
     * @param integer $id 记录id
     * @param string $fields 查询字段
     * @param array $where 查询条件
     * @return \yii\db\ActiveRecord|NULL
     */
    public static function getInfo($id, $fields = '*', $where = []) {

        return self::find()->select($fields)->where(['id' => $id, 'spot_id' => self::$staticSpotId])->andFilterWhere($where)->asArray()->one();
    }

    /*
     * @desc 获取购买某张卡的人数
     * @param integer $packageCardId
     * @return
     */

    public static function getBuyCount($packageCardId) {
        $query = new Query();
        return $query->from(['a' => self::tableName()])
                        ->leftJoin(['b' => MembershipPackageCardUnion::tableName()], '{{a}}.id = {{b}}.membership_package_card_id')
                        ->where(['a.package_card_id' => $packageCardId])
                        ->count('b.id');
    }

    public function validateCardId($attribute, $params) {

        if (!$this->hasErrors()) {

            $count = PackageServiceUnion::getUnionCount($this->package_card_id);
            if ($count == 0) {
                $this->addError($attribute, '该套餐下没有关联状态为正常的服务类型');
            }
        }
    }

    /*
     * @desc 获取用户所有套餐卡
     * @param integer @patientId
     * @return array
     */

    public static function getCardInfo($patientId) {
        $query = new Query();
        $data = $query->from(['a' => self::tableName()])
                ->select(['cardId' => 'a.id', 'serviceId' => 'c.id', 'package_card_service_id' => 'e.id', 'cardName' => 'd.name', 'serviceName' => 'e.name', 'remainTime' => 'c.remain_time', 'a.create_time', 'd.validity_period'])
                ->leftJoin(['b' => MembershipPackageCardUnion::tableName()], '{{a}}.id = {{b}}.membership_package_card_id')
                ->leftJoin(['c' => MembershipPackageCardService::tableName()], '{{a}}.id = {{c}}.membership_package_card_id')
                ->leftJoin(['d' => PackageCard::tableName()], '{{a}}.package_card_id = {{d}}.id')
                ->leftJoin(['e' => PackageCardService::tableName()], '{{c}}.package_card_service_id = {{e}}.id')
                ->where(['patient_id' => $patientId, 'a.status' => 1])
                ->all();
        $cardInfo = [];
        foreach ($data as $value) {
            $cardInfo[$value['cardId']][$value['serviceId']] = $value;
        }
        foreach ($cardInfo as $cardId => $value) {
            $ableUnset = true;
            foreach ($value as $v) {
                if ($v['remainTime'] > 0) {
                    $ableUnset = false;
                }
                $vidateTime = new DateTime((date('Y',$v['create_time']) + $v['validity_period']) . '-' . date('m-d H:i:s',$v['create_time']));//将有限期转为时间戳
            }
            if ($ableUnset) {//卡的服务次数都为0
                unset($cardInfo[$cardId]);
            }else if(time() > $vidateTime->format('U')){//过期
                unset($cardInfo[$cardId]);
            }
        }
        return $cardInfo;
    }

    /*
     * @desc 获取用户对应套餐卡所有服务
     * @param integer @patientId
     * @return array
     */

    public static function getCardBasicInfo($id) {
        $query = new Query();
        $data = $query->from(['a' => self::tableName()])
                ->select(['cardId' => 'a.id', 'serviceId' => 'c.id', 'package_card_service_id' => 'e.id', 'cardName' => 'd.name', 'serviceName' => 'e.name', 'remainTime' => 'c.remain_time'])
                ->leftJoin(['b' => MembershipPackageCardUnion::tableName()], '{{a}}.id = {{b}}.membership_package_card_id')
                ->leftJoin(['c' => MembershipPackageCardService::tableName()], '{{a}}.id = {{c}}.membership_package_card_id')
                ->leftJoin(['d' => PackageCard::tableName()], '{{a}}.package_card_id = {{d}}.id')
                ->leftJoin(['e' => PackageCardService::tableName()], '{{c}}.package_card_service_id = {{e}}.id')
                ->where(['a.id' => $id, 'a.status' => 1])
                ->all();
        $cardInfo = [];
        foreach ($data as $value) {
            $cardInfo[$value['serviceId']] = $value;
        }
        return $cardInfo;
    }

    /**
     * 
     * @param int $memberCardId gzh_membership_package_card主键ID
     * @param int/Array $cardFlowId 流水ID
     * @param int $patientId 患者ID
     * @param type $type 类型 1/消费2/购买3/消费退还
     * @param type $spotId 诊所ID 
     * @return boolean 
     */
    public static function sendMessage($memberCardId, $cardFlowId, $patientId, $spotId = 0) {
        $cardInfo = self::getMmberCard($memberCardId, $cardFlowId, $patientId, $spotId);
//        if ($cardInfo['f_message_subscribe'] == 2) {//用户取消了订阅
//            return;
//        }
        $spotInfo = self::getSpotInfo($spotId);
        \Yii::info('sendMessage spotInfo :[' . json_encode($spotInfo) . ']');
        $parentSpotName = urlencode($spotInfo['parentSpotName']);
        $spotName = urlencode($spotInfo['spotName']);
        $cardName = urlencode($cardInfo['basic']['name']);
        $type = $cardInfo['basic']['type'];
        switch ($type) {
            case 1://1/消费
                $firstFlow = CardFlow::getOneFlow($recordId);
                $template = "【妈咪知道】您在{$spotName}进行{$cardName}套餐卡消费{$cardInfo['trans']},剩余{$cardInfo['left']}。如有疑问，请联系{$spotInfo['telephone']}。";
                break;
            case 2:// 2/购买
                $template = "【妈咪知道】您已成功购买{$cardName},包含{$cardInfo['basic']['content']}。如有疑问，请联系{$spotInfo['telephone']}。";
                break;
            case 3://3/消费退还
                $template = "【妈咪知道】您在{$spotName}进行{$cardName}套餐卡消费退还{$cardInfo['trans']},剩余{$cardInfo['left']}。如有疑问，请联系{$spotInfo['telephone']}。";
                break;
            default :
                $template = '';
                break;
        }
        //发送短信
        Yii::info("cardSendMessage [{$memberCardId}] [{$patientId}] [{$type}] [{$spotId}] [{$cardInfo['basic']['iphone']}] message: [{$template}]");
        if (!empty($template) && $cardInfo['basic']['iphone']) {
            Common::mobileSend($cardInfo['basic']['iphone'], $template);
        }
        return true;
    }

    /**
     * 
     * @param type $memberCardId 会员卡ID
     * @return 根据卡id获取卡信息
     */
    public static function getMmberCard($memberCardId, $cardFlowId, $patientId, $spotId) {
        $spotId = $spotId ? $spotId : $_COOKIE['spotId'];
        $left = (new Query())->from(['a' => MembershipPackageCardService::tableName()])
                        ->select(['a.total_time', 'a.remain_time', 'b.name'])
                        ->leftJoin(['b' => PackageCardService::tableName()], '{{a}}.package_card_service_id={{b}}.id')
                        ->where(['a.spot_id' => $spotId, 'a.membership_package_card_id' => $memberCardId])->all();
        $leftStr = [];
        $transStr = [];
        $basic = (new Query())->from(['a' => MembershipPackageCard::tableName()])
                ->leftJoin(['b' => PackageCard::tableName()], '{{a}}.package_card_id={{b}}.id')
                ->leftJoin(['c' => MembershipPackageCardUnion::tableName()], '{{c}}.membership_package_card_id={{a}}.id')
                ->leftJoin(['d' => Patient::tableName()], '{{c}}.patient_id={{d}}.id')
                ->select(['a.id', 'b.name', 'iphone','content'])->where(['a.id' => $memberCardId, 'a.spot_id' => $spotId, 'd.id' => $patientId])
                ->one();
        $trans = (new Query())->from(['a' => MembershipPackageCardFlow::tableName()])
                ->leftJoin(['b' => MembershipPackageCardFlowService::tableName()], '{{a}}.id={{b}}.flow_id')
                ->leftJoin(['c' => PackageCardService::tableName()], '{{b}}.package_card_service_id={{c}}.id')
                ->select(['a.transaction_type', 'b.time', 'c.name'])
                ->where(['a.id' => $cardFlowId, 'a.spot_id' => $spotId])
                ->all();
        foreach ($left as $val) {
            $leftStr[] = ($val['remain_time'] < 100 ? $val['remain_time'] : '无限') . '次' . $val['name'];
        };
        foreach ($trans as $val) {
            $transStr[] = $val['time'] . '次' . $val['name'];
        }
        $basic['type'] = $trans[0]['transaction_type']; //交易类型
        return ['basic' => $basic, 'left' => implode(',', $leftStr), 'trans' => implode(',', $transStr)];
    }

    public static function getSpotInfo($spotId) {
        $spotId = $spotId ? $spotId : $_COOKIE['spotId'];
        Yii::info('cardrecharge send message spot :【' . $spotId . '】');
        Yii::info('cardrecharge send message wechatSpotId :【' . $_COOKIE['wechatSpotId'] . '】');
        $cacheSuffix = $spotId . Yii::$app->user->identity->id;
        $spot['parentSpotName'] = Yii::$app->cache->get(Yii::getAlias('@parentSpotName') . $cacheSuffix) ? Yii::$app->cache->get(Yii::getAlias('@parentSpotName') . $cacheSuffix) : '';
        $spot['spotName'] = Yii::$app->cache->get(Yii::getAlias('@spotName') . $cacheSuffix) ? Yii::$app->cache->get(Yii::getAlias('@spotName') . $cacheSuffix) : '';
        $spot['parentSpotCode'] = Yii::$app->cache->get(Yii::getAlias('@parentSpotCode') . $cacheSuffix) ? Yii::$app->cache->get(Yii::getAlias('@parentSpotCode') . $cacheSuffix) : '';
        $spot['telephone'] = Spot::getSpot($spotId)['telephone'];
        return $spot;
    }

}
