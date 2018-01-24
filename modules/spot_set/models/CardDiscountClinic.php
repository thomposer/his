<?php

namespace app\modules\spot_set\models;

use Yii;
use app\modules\spot\models\CardDiscount;
use app\modules\spot\models\Tag;

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
class CardDiscountClinic extends \app\common\base\BaseActiveRecord
{

    public function init() {

        parent::init();
        $this->parent_spot_id = $this->parentSpotId;
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%card_discount_clinic}}';
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
            [['spot_id', 'parent_spot_id', 'recharge_category_id', 'tag_id'], 'required'],
            [['card_discount_id', 'spot_id', 'parent_spot_id', 'recharge_category_id', 'tag_id', 'create_time', 'update_time'], 'integer'],
            [['discount'], 'number'],
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
     * @param type $catId 卡种ID
     * @return 获取卡种的标签 折扣list  以机构配置的标签为主
     */
    public static function cardDiscountClinic($catId) {
        $parentTag = CardDiscount::find()->select(['spot_id', 'recharge_category_id', 'tag_id', 'id'])->where(['spot_id' => self::$staticParentSpotId, 'recharge_category_id' => $catId])->asArray()->all();
        $clinicTag = self::find()->select(['card_discount_id', 'parent_spot_id', 'spot_id', 'recharge_category_id', 'tag_id', 'discount'])->where(['spot_id' => self::$staticSpotId, 'recharge_category_id' => $catId])->indexBy('tag_id')->asArray()->all();
        $tagIdData = array_column($parentTag, 'tag_id');
        $tagInfo = Tag::find()->select(['id', 'name'])->where(['spot_id' => self::$staticParentSpotId, 'id' => $tagIdData])->indexBy('id')->asArray()->all();
        foreach ($parentTag as $key => &$v) {
            if (isset($clinicTag[$v['tag_id']])) {
                $v['discount'] = $clinicTag[$v['tag_id']]['discount'];
            } else {
                $v['discount'] = '100.00';
            }
            $v['name'] = ($v['tag_id'] == 0) ? '诊金' : $tagInfo[$v['tag_id']]['name'];
        }
        return $parentTag;
    }

    /**
     * 
     * @param type $catId 卡种ID
     * @return 获取卡种的标签 折扣list  以诊所配置的折扣为主
     */
    public static function cardDiscountListClinic($catId) {
        $clinicTag = self::find()->select(['card_discount_id', 'parent_spot_id', 'spot_id', 'recharge_category_id', 'tag_id', 'discount'])->where(['spot_id' => self::$staticSpotId, 'recharge_category_id' => $catId])->indexBy('tag_id')->asArray()->all();
        $tagIdData = array_keys($clinicTag);
        $tagInfo = Tag::find()->select(['id', 'name'])->where(['spot_id' => self::$staticParentSpotId, 'id' => $tagIdData])->indexBy('id')->asArray()->all();
        foreach ($clinicTag as $key => &$v) {
            if (isset($clinicTag[$v['tag_id']])) {
                $v['discount'] = $clinicTag[$v['tag_id']]['discount'];
            } else {
                $v['discount'] = '100.00';
            }
            if ($v['tag_id'] == 0) {
                $v['name'] = '诊金';
            } else {
                if (isset($tagInfo[$v['tag_id']])) {
                    $v['name'] = $tagInfo[$v['tag_id']]['name'];
                } else {
                    unset($clinicTag[$key]);
                }
            }
        }
        return $clinicTag;
    }

}
