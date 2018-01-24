<?php

namespace app\modules\spot_set\models;


use Yii;
use app\modules\spot\models\Spot;
use app\modules\spot\models\OrganizationType;
use yii\db\Query;

/**
 * This is the model class for table "{{%spot_type}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $type
 * @property string $time
 * @property integer $status(1-正常，2-禁用)
 * @property string $create_time
 * @property string $update_time
 * @property string $third_platform
 * @property Spot $spot
 */
class SpotType extends \app\common\base\BaseActiveRecord
{
    public $thirdPlatform;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%spot_type}}';
    }

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'create_time', 'update_time','status','time','organization_type_id'], 'integer'],
            [['spot_id'], 'exist', 'skipOnError' => true, 'targetClass' => Spot::className(), 'targetAttribute' => ['spot_id' => 'id']],
            ['organization_type_id', 'validateType'],
            ['type','string','max' => 15],
            [['is_delete'],'default','value' => 0],
            [['organization_type_id','time'],'required'],
            [['status'],'default','value' => 1],
            [['thirdPlatform'],'safe'],
//            ['time', 'validateTime'],
        ];
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['custom'] = ['spot_id','create_time','update_time','thirdPlatform','type','time','organization_type_id'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'spot_id' => '诊所id',
            'organization_type_id' => '服务名称',
            'third_platform'=>'允许开放预约平台',
            'type' => '服务名称',
            'time' => '时长',
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
     * @param 获取第三方平台信息
     */

    public static  $getThirdPlatform=[
        1=>'妈咪知道',
        2=>'就医160',
    ];

    /**
     * @param $id  传过来的服务类型ID
     * @return string  返回第三方平台查询预约名称并且通过字符串连接返回或者返回ID标记修改中预约的平台
     */
    public static function getThirdPlatform($id){
        if(($result=ThirdPlatform::find()->select('platform_id')->where(['spot_id'=>parent::$staticSpotId,'spot_type_id'=>$id])->orderBy('id')->asArray()->all())!==null){
           $data=[];
                foreach ($result as $key => $val) {
                    $data['platform_name'][$key] = self::$getThirdPlatform[$val['platform_id']];  //通过$val['platform_id']ID查询名称显示
                    $data['id'][$key] = $val['platform_id'];//查询ID返回
                }
                $data['platform_name']=$data['platform_name']?implode('、', $data['platform_name']):'';
                return $data;
        }
    }
    
    /**
     * @param $dataProvider
     * @return string  返回第三方平台查询预约名称,根据spot_type_id分类
     */
    public static function getThirdPlatformByList($spotTypeIdList){
        $query = new Query();
        $data = $query->from(['a' => ThirdPlatform::tableName()])
                ->select(['spot_type_id','platform_id'])
                ->where(['spot_type_id' => $spotTypeIdList, 'spot_id' => self::$staticSpotId])
                ->orderBy('id')
                ->all();
        $platformNameList = [];
        foreach ($data as $value) {
            $platformNameList[$value['spot_type_id']][] = self::$getThirdPlatform[$value['platform_id']];
        }
        return $platformNameList;
    }






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
            if(!$this->checkType()){
                $this->addError($attribute, '该服务类型不存在');
            }
        } else {
            $oldDescription = $this->getOldAttribute($attribute);
            if ($oldDescription != $this->$attribute) {
                $hasRecord = $this->checkDuplicate($attribute, $this->$attribute);
                if ($hasRecord) {
                    $this->addError($attribute,'服务名称不能重复');
                }
            }
            if($this->status == 1 && !$this->checkType()){
                $this->addError($attribute, '该服务类型不存在');
            }
        }
    }
    
    public function beforeSave($insert) {
        $this->type = OrganizationType::getSpotType('id = '.$this->organization_type_id)[0]['name'];
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
        return self::find()->select(['id','name'=>'type','time'])->where(['spot_id'=>self::$staticSpotId])->andWhere($where)->asArray()->all();
    }
    /**
     * 
     * @param integer $id 预约服务id
     * @param string|array $fields 预约服务-字段组合
     * @return \yii\db\ActiveRecord|NULL
     * @desc 返回对应的预约服务id的字段信息
     */
    public static function getTypeFields($id,$fields = '*'){
        
        return self::find()->select($fields)->where(['id' => $id,'spot_id' => self::$staticSpotId])->asArray()->one();
    }
    protected function checkDuplicate($attribute, $params) {
        $hasRecord = SpotType::find()->select(['organization_type_id'])->where([$attribute => trim($this->$attribute),'spot_id' => $this->spot_id])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }
    
    protected function checkType(){
        $hasRecord = OrganizationType::find()->select(['id'])->where(['id' => $this->organization_type_id,'spot_id' => self::$staticParentSpotId,'status' => 1])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }
}
