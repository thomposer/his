<?php

namespace app\modules\spot\models;


use Yii;
use app\modules\spot\models\Spot;

/**
 * This is the model class for table "{{%organization_type}}".
 *
 * @property string $id
 * @property string $spot_id （机构ID）
 * @property string $name
 * @property string $time
 * @property integer $status(1-正常，2-禁用)
 * @property string $create_time
 * @property string $update_time
 *
 * @property Spot $spot
 */
class OrganizationType extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%organization_type}}';
    }

    public function init() {
        parent::init();
        $this->spot_id = $this->parentSpotId;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'create_time', 'update_time','status','time','record_type'], 'integer'],
            [['spot_id'], 'exist', 'skipOnError' => true, 'targetClass' => Spot::className(), 'targetAttribute' => ['spot_id' => 'id']],
            ['name', 'validateType'],
            ['name','string','max' => 15],
//            [['is_delete'],'default','value' => 0],
            [['name','time','status','record_type'],'required'],
            [['status'],'default','value' => 1],
            [['record_type'],'default','value' => 0],
//            ['time', 'validateTime'],
        ];
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['custom'] = ['spot_id','create_time','update_time','name','status','time','record_type'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'spot_id' => '机构id',
            'name' => '服务名称',
            'time' => '时长',
            'record_type' => '病历',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @var 时长
     */
    public static $getTime = [
        10 => '10',
        20 => '20',
        30 => '30',
        40 => '40',
        50 => '50',
        60 => '60',
        90 => '90',
        120 => '120',
    ];


    /**
     * @var 时长
     */
    public static $getStatus = [
        1 => '正常',
        2 => '停用',
    ];
    
    /**
     * @var 病历类型
     */
    public static $getRecordType = [
        1 => '非专科病历',
        2 => '专科病历--儿保',
//        3 => '专科病历--口腔',
        4 => '专科病历--口腔初诊',
        5 => '专科病历--口腔复诊',
        6 => '专科病历--正畸初诊',
        7 => '专科病历--正畸复诊',
    ];

    public function validateTime($attribute,$params){
        if(!$this->hasErrors()){
            if(count($this->time) > 0){
                foreach ($this->time as $key => $v){
                    if ($this->time[$key] == null) {
                        $this->addError($attribute, '时间间隔不能为空');
                    }else  if(!preg_match("/^\s*[+-]?\d+\s*$/",$this->time[$key])){
                        $this->addError($attribute,'时间间隔必须是一个整数');
                    }
                }
            }else{
                $this->addError($attribute, '类型必须有一条');
            }
        }
    }

    public function validateType($attribute,$params){
        if ($this->isNewRecord) {
            if ($this->checkDuplicate($attribute, $this->$attribute)) {
                $this->addError($attribute, '服务名称不能重复');
            }
        } else {
            $oldDescription = $this->getOldAttribute($attribute);
            if ($oldDescription != $this->$attribute) {
                $hasRecord = $this->checkDuplicate($attribute, $this->$attribute);
                if ($hasRecord) {
                    $this->addError($attribute,'服务名称不能重复');
                }
            }
        }
    }

    public function beforeSave($insert) {
        $this->name = trim($this->name);
        return parent::beforeSave($insert);
    }

    /**

     * @return \yii\db\ActiveQuery
     */
    public function getSpot()
    {
        return $this->hasOne(Spot::className(), ['id' => 'spot_id']);
    }
    /**
     * @param string $where 查询条件
     * @return 获取当前诊所所有的预约类型
     */
    public static function getSpotType($where = null){
        if(!$where){
            $where = '1 != 0';
        }
        return self::find()->select(['id','name','time','status'])->where(['spot_id'=>self::$staticParentSpotId])->andWhere($where)->asArray()->all();
    }
    /**
     *
     * @param integer $id 预约服务id
     * @param string|array $fields 预约服务-字段组合
     * @return \yii\db\ActiveRecord|NULL
     * @desc 返回对应的预约服务id的字段信息
     */
    public static function getTypeFields($id,$fields = '*'){

        return self::find()->select($fields)->where(['id' => $id,'spot_id' => self::$staticParentSpotId])->asArray()->one();
    }
    protected function checkDuplicate($attribute, $params) {
        $hasRecord = self::find()->select(['name'])->where([$attribute => trim($this->$attribute),'spot_id' => $this->spot_id])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }
}
