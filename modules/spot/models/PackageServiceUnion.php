<?php

namespace app\modules\spot\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "gzh_package_service_union".
 *
 * @property string $id
 * @property string $parent_spot_id
 * @property string $package_card_id
 * @property string $package_card_service_id
 * @property integer $time
 * @property string $create_time
 * @property string $update_time
 *
 * @property PackageCard $packageCard
 * @property PackageCardService $packageCardService
 */
class PackageServiceUnion extends \app\common\base\BaseActiveRecord
{
    public function init() {
        parent::init();
        $this->spot_id = $this->parentSpotId;
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%package_service_union}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'package_card_service_id', 'time'], 'required'],
            [['spot_id', 'package_card_id', 'create_time', 'update_time'], 'integer'],
            ['time', 'validateTime'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'spot_id' => '机构id',
            'package_card_id' => '套餐卡id',
            'package_card_service_id' => '套餐卡服务类型id',
            'time' => '次数',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
    /*
     * 验证服务名称与次数
     */
    public function validateTime($attribute) {
        $packageCardServiceList = [];
        foreach ($this->package_card_service_id as $key => $value) {
            if(empty($this->package_card_service_id[$key])){//服务类型id为空
                $this->addError($attribute, '服务类型不能为空');
            }
            if(empty($this->time[$key])){//次数为空
                $this->addError($attribute, '次数不能为空');
            }
            if(!preg_match("/^\s*[+-]?\d+\s*$/",$this->time[$key])){//次数不为整数
                $this->addError($attribute, '次数必须为1-999的整数');
            }
            if($this->time[$key] < 1 || $this->time[$key] > 999){
                $this->addError($attribute, '次数必须为1-999的整数');
            }
            if(!isset($packageCardServiceList[$value])){
                $packageCardServiceList[$value] = 1;
            }else{//服务类型重复
                $this->addError($attribute, '服务类型不能重复');
            }
        }
    }
    
    /*
     * 获取关联列表
     */
   public static function getUnionList($package_card_id,$where = []){
        return self::find()->select(['package_card_service_id','time'])->where(['spot_id' => self::$staticParentSpotId, 'package_card_id' => $package_card_id])->andFilterWhere($where)->asArray()->all();
   }
   /**
    * @desc 获取该套餐卡所关联的服务类型为正常的数量
    * @param integer $package_card_id 套餐卡id
    */
   public static function getUnionCount($package_card_id){
       
       $query = new Query();
       $query->from(['a' => self::tableName()]);
       $query->leftJoin(['b' => PackageCardService::tableName()],'{{a}}.package_card_service_id = {{b}}.id');
       $query->where(['a.package_card_id' => $package_card_id,'a.spot_id' => self::$staticParentSpotId,'b.status' => 1]);
       return $query->count(1);
   }
}
