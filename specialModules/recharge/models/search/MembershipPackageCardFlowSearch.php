<?php

namespace app\specialModules\recharge\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\specialModules\recharge\models\MembershipPackageCardFlow;
use app\specialModules\recharge\models\MembershipPackageCardUnion;
use app\modules\patient\models\Patient;
use yii\db\ActiveQuery;
use app\modules\spot\models\PackageCardService;
use app\specialModules\recharge\models\MembershipPackageCardFlowService;

/**
 * CardRechargeSearch represents the model behind the search form about `app\specialModules\recharge\models\CardRecharge`.
 */
class MembershipPackageCardFlowSearch extends MembershipPackageCardFlow
{

    public $patientName;
    public $channelSource;
    public $sex;
    public $birthday;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'membership_package_card_id'], 'integer'],
            [['username'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '诊所ID(来源渠道)',
            'member_card_id' => '用户套餐卡ID',
            'user_id' => '操作用户ID',
            'flow_item' => '交易项',
            'patientName' => '交易用户',
            'trans_detail' => '明细',
            'transaction_type' => '交易类型',
            'channelSource' => '来源渠道',
            'username' => '操作人',
            'pay_type' => '支付方式',
            'operate_origin' => '操作渠道',
            'remark' => '备注',
            'charge_record_id' => '收费记录id',
            'charge_record_log_id' => '收费交易流水id',
            'create_time' => '时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 20) {
        $query = new ActiveQuery(self::className());
        $query->from(['a' => self::tableName()]);
        $query->select(['a.id', 'a.spot_id', 'a.membership_package_card_id', 'a.flow_item', 'a.transaction_type', 'a.price',
            'a.username', 'a.pay_type', 'a.operate_origin', 'a.remark', 'a.charge_record_log_id', 'a.create_time', 'patientName' => 'c.username',
            'patient_id' => 'c.id', 'c.sex', 'c.birthday'
        ]);
        $query->leftJoin(['b' => MembershipPackageCardUnion::tableName()], '{{a}}.membership_package_card_id={{b}}.membership_package_card_id')
                ->leftJoin(['c' => Patient::tableName()], '{{b}}.patient_id={{c}}.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
                'attributes' => ['id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        $query->where(['a.membership_package_card_id' => $params['membership_package_card_id']]);

        $query->andFilterWhere([
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'username', trim($this->username)]);

        return $dataProvider;
    }

    /**
     * 
     * @param type $followIds 流水ID 
     */
    public static function getTransDetail($followIds) {
        $data = (new \yii\db\Query())
                ->from(['a' => MembershipPackageCardFlowService::tableName()])
                ->select(['b.name', 'a.time', 'a.flow_id', 'c.transaction_type'])
                ->leftJoin(['b' => PackageCardService::tableName()], '{{a}}.package_card_service_id={{b}}.id')
                ->leftJoin(['c' => MembershipPackageCardFlow::tableName()], '{{a}}.flow_id={{c}}.id')
                ->where(['a.flow_id' => $followIds])
                ->all();
        $res = [];
        if (!empty($data)) {
            foreach ($data as &$val) {
                $sign = ($val['transaction_type'] == 1) ? '-' : '+';
                $val['detail'] = $val['name'] . ' ' . $sign . $val['time'];
                if (isset($res[$val['flow_id']])) {
                    $res[$val['flow_id']] .= $val['detail'] . ' ';
                } else {
                    $res[$val['flow_id']] = $val['detail'] . ' ';
                }
            }
        }
        return $res;
    }

}
