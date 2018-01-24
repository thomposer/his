<?php

namespace app\modules\overview\models;

use app\modules\spot\models\Spot;
use Yii;

class Overview extends \app\modules\spot\models\Spot
{

    public $spot_num;

    public function attributeLabels() {
        return [
            'spot_name' => '名称',
            'contact_iphone' => '联系人手机',
            'contact_name' => '联系人',
            'create_time' => '创建时间',
            'spot_num' => '诊所数量'
        ];
    }

    /**
     * @return  返回诊所总数
     * @param $id 机构id
     */
    public static function getTotal($id) {
        return Spot::find()->where(['parent_spot' => $id, 'status' => 1])->count();
    }

    /**
     * 
     * @return  诊所/机构 总数 Description
     */
    public static function getSpotNum() {
        $query = new \yii\db\Query();
        $spotNum = self::find()->where(['!=', 'parent_spot', 0])->andWhere(['status' => 1])->count();
        $agencyNum = self::find()->where(['parent_spot' => 0, 'status' => 1])->count();
        return [
            'spotNum' => $spotNum,
            'agencyNum' => $agencyNum,
        ];
    }

    /**
     * @param $id
     * @return $this
     */
    public static function getSpotCode($id) {
        return Spot::find()->select(['spot'])->where(['id' => $id, 'status' => 1])->asArray()->one();
    }

    /**
     * @param $id
     * @return $this
     */
    public static function getSpotName($id,$where = '1 != 0') {
        return Spot::find()->select(['spot_name'])->where(['id' => $id, 'status' => 1])->asArray()->one();
    }

    /**
     * 
     * @param 诊所ID $spot_id
     * @return 根据诊所ID返回其机构信息 Description
     */
    public static function getParentSpotName($spot_id) {
        $agency = Spot::find()->select(['id', 'parent_spot'])->where(['id' => $spot_id])->asArray()->one();
        return Spot::find()->select(['spot_name'])->where(['id' => $agency['parent_spot']])->asArray()->one();
    }

}
