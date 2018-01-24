<?php

namespace app\modules\spot\models;

use Yii;

/**
 * This is the model class for table "{{%card_discount}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $recharge_category_id
 * @property integer $tag_id
 * @property string $discount
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property CardRechargeCategory $rechargeCategory
 */
class CardDiscount extends \app\common\base\BaseActiveRecord
{

    public function init() {

        parent::init();
        $this->spot_id = $this->parentSpotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%card_discount}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('cardCenter');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'recharge_category_id', 'tag_id'], 'required'],
            [['spot_id', 'recharge_category_id', 'tag_id', 'create_time', 'update_time'], 'integer'],
            [['recharge_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => CardRechargeCategory::className(), 'targetAttribute' => ['recharge_category_id' => 'f_physical_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'spot_id' => '机构id',
            'recharge_category_id' => '卡种id',
            'tag_id' => '标签id',
            'discount' => '折扣',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRechargeCategory() {
        return $this->hasOne(CardRechargeCategory::className(), ['f_physical_id' => 'recharge_category_id']);
    }

    /**
     * 
     * @param integer $id 卡种id
     * @return \yii\db\ActiveRecord[]|unknown
     * @desc 返回卡种对应的标签的信息
     */
    public static function getCardTagDiscount($id) {
        $cardDiscountList = CardDiscount::find()->select(['tag_id'])->where(['spot_id' => self::$staticParentSpotId, 'recharge_category_id' => $id])->indexBy('tag_id')->asArray()->all();
        $tagIdData = array_keys($cardDiscountList);
        $tagInfo = Tag::find()->select(['id', 'name'])->where(['spot_id' => self::$staticParentSpotId, 'id' => $tagIdData])->indexBy('id')->asArray()->all();
        foreach ($cardDiscountList as $key => $v) {
//             $name = Tag::find()->select(['name'])->where(['spot_id' => self::$staticParentSpotId,'id' => $v['tag_id']])->asArray()->one()['name'];
            if (isset($tagInfo[$key])) {
                $cardDiscountList[$key]['name'] = $tagInfo[$key]['name'];
            } elseif ($key == 0) {
                $cardDiscountList[$key]['name'] = '诊金';
            } else {
                unset($cardDiscountList[$key]);
            }
        }
        return $cardDiscountList;
    }

    public function getSpot_name() {

        return $this->hasOne(Spot::className(), ['id' => 'spot_id'])->select(['spot_name']);
    }

}
