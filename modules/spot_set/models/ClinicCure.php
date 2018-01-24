<?php

namespace app\modules\spot_set\models;

use app\modules\spot\models\CureList;
use Yii;
use yii\db\Query;
use app\modules\spot\models\Tag;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;
/**
 * This is the model class for table "{{%curelist_clinic}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $cure_id
 * @property string $price
 * @property string $default_price
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 */
class ClinicCure extends \app\common\base\BaseActiveRecord
{

    public $unit;//单位
    public $name;//名字
    public $meta;//拼音码
    public $remark;//备注
    public $tag_id;//标签id
    public $tag_name;
    public $international_code;//国际编码
    public $type;
    
    public function init(){
        
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%curelist_clinic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'cure_id','price'], 'required'],
            [['spot_id', 'cure_id', 'status', 'tag_id', 'type','create_time','update_time'], 'integer'],
            [['price', 'default_price'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '最多保留两位小数.'],
            [['unit', 'name', 'meta', 'remark', 'international_code', 'tag_name'], 'string'],
            [['price', 'default_price'],'number','min' => 0,'max' => 100000],
            [['cure_id'],'validateCure'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '诊所id',
            'cure_id' => '医嘱名称',
            'price' => '零售价',
            'default_price' => '成本价',
            'status' => '状态',
            'unit' => '单位',
            'meta' => '拼音码',
            'remark' => '备注',
            'tag_id' => '标签',
            'tag_name' => '标签名称',
            'name' => '医嘱名称',
            'international_code' => '国际编码',
            'create_time' => '创建时间',
            'update_time' => '更新时间'
        ];
    }
    /**
     * @desc 修改该机构治疗id底下关联的诊所治疗配置的状态
     * @param integer $cureId 机构治疗id
     * @param integer $status 状态
     */
    public static function updateCureStatus($cureId, $status) {
        Yii::$app->db->createCommand()
            ->update(self::tableName(), ['status' => $status], ['cure_id' => $cureId])
            ->execute();
    }
    /**
     * 
     * @desc 返回对应诊所的治疗项目正常的基本信息
     * @param integer $spotId 诊所id
     */
    public static function getCureList($spotId = NULL,$where = '1 != 0') {
        if(empty($spotId)){
            $spotId = self::$staticSpotId;
        }
        return (new Query())
            ->select([
                "a.id", "a.spot_id", "a.cure_id", "a.price", "a.default_price", "a.status",
                "b.name", "b.unit", "b.meta", "b.remark", "b.tag_id", "b.international_code"
            ])
            ->from(["a" => self::tableName()])
            ->leftJoin(["b" => CureList::tableName()], "a.cure_id = b.id")
            ->where(["a.spot_id" => $spotId, "a.status" => 1])
            ->andFilterWhere($where)
            ->orderBy(["a.id" => SORT_DESC])
            ->indexBy("id")
            ->all();
    }
    /**
     * 
     * @param integer $id 诊所治疗id
     * @param string|array $where 查询条件
     * @return 返回该诊所治疗id的基本信息
     */
    public static function getCure($id, $where = '1 != 0') {
        return (new Query())
            ->select([
                "a.id", "a.spot_id", "a.cure_id", "a.price", "a.default_price", "a.status",
                "b.name", "b.unit", "b.meta", "b.remark", "b.tag_id", "b.international_code"
            ])
            ->from(["a" => self::tableName()])
            ->leftJoin(["b" => CureList::tableName()], "a.cure_id = b.id")
            ->leftJoin(["c" => Tag::tableName()], "b.tag_id = c.id")
            ->where(["a.spot_id" => self::$staticSpotId])
            ->andFilterWhere(["a.id" => $id])
            ->andWhere($where)
            ->one();
    }

    /**
     * @param $attribute
     * @param $params
     * @return bool   返回治疗医嘱唯一性判断
     */
    public function validateCure($attribute,$params){

        if(!$this->hasErrors()){
            $hasRecord = self::find()->where(['cure_id' =>$this->cure_id,'spot_id' =>$this->spotId])->count();
            if($this->isNewRecord){
                $unionClinicRecord = ConfigureClinicUnion::find()->where(['configure_id' => $this->cure_id, 'spot_id' => $this->spotId, 'type' => ChargeInfo::$cureType])->count();
                if (empty($unionClinicRecord)) {
                    $this->addError('cure_id', '该治疗医嘱已取消关联');
                }
                if($hasRecord){
                    $this->addError('cure_id',   '该治疗医嘱已存在');
                }
            }else{
                $oldCureId = $this->getOldAttribute('cure_id');
                if($oldCureId != $this->cure_id && $hasRecord){

                    $this->addError('cure_id',   '该治疗医嘱已存在');

                }
            }
        }
    }
    

}
