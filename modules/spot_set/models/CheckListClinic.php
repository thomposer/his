<?php

namespace app\modules\spot_set\models;
use app\modules\spot\models\CheckList;
use Yii;
use yii\db\Query;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;

/**
 * This is the model class for table "{{%checklist_clinic}}".
 *
 * @property integer $id id
 * @property string $spot_id 诊所id
 * @property string $check_id 检查医嘱id
 * @property string $price 零售价
 * @property string $default_price 成本价
 * @property integer $status 状态
 * @property string $name 检查医嘱名称
 * @property integer $unit 检查医嘱单位
 * @property string $meta 检查医嘱拼音码
 * @property string $remark 检查医嘱备注
 * @property string $international_code 检查医嘱国际编码
 *
 * @property Checklist $check
 */
class CheckListClinic extends \app\common\base\BaseActiveRecord
{
    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    public $name;//检查医嘱名称
    public $unit;//检查医嘱单位
    public $meta;//检查医嘱拼音码
    public $remark;//检查医嘱备注
    public $international_code;//检查医嘱国际编码
    public $tagName;//标签名称

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%checklist_clinic}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['price','check_id'], 'required'],
            [['spot_id', 'check_id', 'status'], 'integer'],
            [['price', 'default_price'], 'number'],
            [['check_id'], 'exist', 'skipOnError' => true, 'targetClass' => CheckList::className(), 'targetAttribute' => ['check_id' => 'id']],
            ['status','default','value' => 1],
            [['price','default_price'], 'match', 'pattern' => '/^([0-9][0-9]*)+(.[0-9]{1,2})?$/', 'message' => '最多保留两位小数.'],
            [['price','default_price'], 'number', 'max' => 100000],
            [['name','unit','meta','remark','international_code','tagName'],'safe'],
            [['check_id'],'validateCheck'],
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
            'check_id' => '影像学检查',
            'price' => '零售价',
            'default_price' => '成本价',
            'status' => '状态',
            'name' => '名称',
            'unit' => '单位',
            'meta' => '拼音码',
            'remark' => '备注',
            'international_code' => '国际编码',
            'tagName' => '标签',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    //医嘱状态
    public static $status = [
        1 => '正常',
        2 => '停用'
    ];

    /**
     * @return array 获取当前诊所下的状态为启用的检查医嘱
     */
    public static function getCheckListAll($where = []){
        $query = new Query();
        $query->from(['a' => CheckListClinic::tableName()]);
        $query->select(['a.id', 'a.price','b.name', 'b.unit', 'b.tag_id','b.meta']);
        $query->leftJoin(['b' => CheckList::tableName()],'{{a}}.check_id = {{b}}.id');
        $query->where(['a.spot_id' => self::$staticSpotId, 'a.status' => 1]);
        $query->andFilterWhere($where);
        $query->orderBy(['a.id' => SORT_DESC]);
        $query->indexBy('id');
        $result = $query->all();
        return $result;
    }

    /**
     *
     * @desc 返回对应诊所的检验医嘱正常的基本信息
     * @param integer $spotId 诊所id
     */
    public static function getCheckList($spotId,$where = '1 != 0') {
        if(empty($spotId)){
            $spotId = self::$staticSpotId;
        }
        return (new Query())
            ->select([
                "a.id", "a.spot_id", "a.check_id", "a.price", "a.default_price", "a.status",
                "b.name", "b.unit", "b.meta", "b.remark", "b.tag_id", "b.international_code"
            ])
            ->from(["a" => self::tableName()])
            ->leftJoin(["b" => CheckList::tableName()], "a.check_id = b.id")
            ->where(["a.spot_id" => $spotId, "a.status" => 1])
            ->andWhere($where)
            ->orderBy(["a.id" => SORT_DESC])
            ->indexBy("id")
            ->all();
    }

    /**
     * @param $attribute
     * @param $params
     * @return bool   返回影像学检查唯一性判断
     */
    public function validateCheck($attribute,$params){

        if(!$this->hasErrors()){
            $hasRecord = self::find()->where(['check_id' =>$this->check_id,'spot_id' =>$this->spotId])->count();
            if($this->isNewRecord){
                $unionClinicRecord = ConfigureClinicUnion::find()->where(['configure_id' => $this->check_id, 'spot_id' => $this->spotId, 'type' => ChargeInfo::$checkType])->count();
                if (empty($unionClinicRecord)) {
                    $this->addError('check_id', '该影像学检查已取消关联');
                }
                if($hasRecord){
                    $this->addError('check_id',   '该影像学检查已存在');
                }
            }else{
                $oldCheckId = $this->getOldAttribute('check_id');
                if($oldCheckId != $this->check_id && $hasRecord){
                        $this->addError('check_id',   '该影像学检查已存在');

                }
            }
        }
    }

}
