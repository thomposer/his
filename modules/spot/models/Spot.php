<?php

namespace app\modules\spot\models;

use Yii;
use app\common\base\BaseActiveRecord;
use yii\db\Query;
use yii\helpers\Url;
use app\modules\user\models\UserSpot;

/**
 * This is the model class for table "{{%spot}}".
 *
 * @property integer $id
 * @property string $spot
 * @property integer $status
 * @property string $user_id
 * @property string $spot_name
 * @property string $template
 * @property string $address
 * @property string $detail_address
 * @property string $fax_number
 * @property string $telephone
 * @property string $icon_url
 * @property string $contact_name
 * @property string $contact_email
 * @property string $province
 * @property string $city
 * @property string $area
 * @property integer $parent_spot
 * @property integer $contact_iphone
 * @property integer $create_time
 * @property integer $update_time
 * @property integer $addSelected 我将加入该诊所
 * @property string $appointment_type 预约类型
 * @property integer $type 类型(1-机构,2-诊所)
 */
class Spot extends BaseActiveRecord
{
	
	const HAS_RENDER = 1;
	const NO_REDNER = 0;
	public static $RENDER_STATUS = array(0, 1);
	public $addSelected;
	public $address;
	public function init(){
	    parent::init();
	    $this->type = 2;
	}
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%spot}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['template','province','city','area', 'spot','spot_name','contact_iphone','contact_name','icon_url','fax_number','telephone','contact_email','address','detail_address'], 'trim'],
            [['addSelected','template', 'spot','spot_name','parent_spot','type','contact_iphone','contact_name','contact_email','appointment_type'], 'required'],
            [['template','spot_name','contact_name','fax_number','province','city','area','contact_email','address','detail_address','appointment_type'], 'string'],
            [['spot'], 'string', 'max' => 32],
            ['spot_name','string','max' => 30],
            ['telephone','string','max' => 32],
//             ['telephone','match','pattern' => '/^([0-9]{3,4}-)?[0-9]{7,8}$/'],
            [['icon_url','province','city','area'], 'string', 'max' => 64],                                  
            [['spot'], 'unique', 'message' => '该代码已经被占用'],
            [['status','parent_spot','type','create_time','update_time'],'integer'],
            [['contact_iphone'],'match','pattern' => '/^\d{11}$/'],
            [['user_id'],'safe'],
            ['spot_name','validateSpotName','on' => 'spot'],
            ['status','validateSpotStatus','on' => 'spot'],
            ['contact_email','email'],
            [['province','city','area'],'default','value' => ''],
            [['spot'], 'string', 'max' => 10,'on' => 'organization'],
            ['spot','match','pattern' => '/^[a-zA-Z0-9]+$/','on' => 'organization'],
            
            
        ];
    }
    public function scenarios(){
        
        $parent = parent::scenarios();
        $parent['organization'] = ['spot','address','type','spot_name','contact_name','contact_iphone','contact_email','province','city','area','detail_address','template','create_time','update_time'];//机构场景
        $parent['spot'] = ['user_id','parent_spot','type','spot','spot_name','contact_name','contact_iphone','fax_number','province','city','area','address','detail_address','icon_url','telephone','status'];//诊所场景
        $parent['createSpot'] = ['addSelected','user_id','type','parent_spot','spot','spot_name','contact_name','contact_iphone','fax_number','province','city','area','address','detail_address','icon_url','telephone','status'];//诊所场景
        return $parent;
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户简称',
            'template' =>'诊所模板',
            'spot' => '诊所代码',
            'icon_url' => '诊所图标',
            'status' => '状态',
            'spot_name' => '诊所名称',
            'contact_iphone' => '联系人手机',
            'contact_name' => '联系人',
            'contact_email' => '联系人邮箱',
            'detail_address' => '地址',
            'telephone' => '电话',
            'fax_number' => '传真',
            'address' => '省/市/区',
            'detail_address' => '街道/详细地址',
            'update_time' => '更新时间',
            'create_time' => '创建时间',
            'addSelected' => '我将加入该诊所',
            'appointment_type'=>'预约类型',
        ];
    }
    public static $getStatus = [
        1 => '正常',
        2 => '停用',
//         3 => '删除'
    ];

    public static $appointmentType = [
       1 => '按医生预约',
       2 => '按科室预约',
    ];

    /**
     * 获取当前站点对象
     * @return Ambigous <\yii\db\ActiveRecord, multitype:, NULL>
     */
    public static function getSpot($spotId = 0) {
        $spot_id = $spotId ? $spotId : self::$staticSpotId;
        return Spot::find()->select(['spot_name', 'spot', 'status', 'province', 'city', 'area', 'telephone', 'icon_url'])->where(['id' => $spot_id])->asArray()->one();
    }

    /**
     * 获取机构名称
     * @return mixed
     */
    public static function getSpotName($spotId,$where = '1 != 0') {
        
        $Spot=Spot::find()->select(['spot_name','spot','status'])->where(['id' => $spotId])->andWhere($where)->one();
        return $Spot['spot_name'];
    }

    public function validateSpotName($attribute){
        $parentSpotId = $this->parentSpotId;
        if ($this->isNewRecord) {
            $hasRecord = Spot::find()->select(['id'])->where(['parent_spot' => $parentSpotId, $attribute => $this->$attribute])->asArray()->limit(1)->one();
            if ($hasRecord) {
                $this->addError($attribute, '该诊所名称已存在');
            }
        } else {
            $oldSpotName = $this->getOldAttribute('spot_name');
            if ($oldSpotName != $this->spot_name) {
                $hasRecord = $this->checkDuplicate('spot_name', $this->spot_name);
                if ($hasRecord) {
                    $this->addError('spot_name',   '该诊所名称已存在');
                }
            }
        }
    }
    
    public function validateSpotStatus($attribute){
        $parentSpotId = $this->parentSpotId;
        if($this->isNewRecord){
            $spotCount = Spot::find()->where(['parent_spot' => $parentSpotId,'status' => '1'])->count();
        }else{
            $spotCount = Spot::find()->where(['parent_spot' => $parentSpotId,'status' => '1'])->andWhere(['<>','id',$this->id])->count();
        }
        if($this->status != 1 && $spotCount < 1){
                $this->addError('status',   '操作失败，至少需要有一个使用中的诊所');
        }
    }
    
    protected function checkDuplicate($attribute, $params) {
        $parentSpotId = $this->parentSpotId;
        $hasRecord = Spot::find()->select(['id'])->where(['parent_spot' => $parentSpotId, $attribute => $params])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * @property 缓存该用户在当前机构下的诊所列表信息
     * @return void|mixed|boolean|\yii\caching\Dependency|\yii\caching\false
     */
    public function getCacheSpotList($id = NULL){
        $cache = Yii::$app->cache;
        $userId = $id ? $id : $this->userInfo->id;
        
        $spotListCache = Yii::getAlias('@spotList').$this->parentSpotId.'_'.$userId;
        if(!$cache->get($spotListCache)){
            $query = new Query();
            $query->from(['a' => Spot::tableName()]);
            $query->select(['a.id','a.spot_name']);
             
            if(Yii::$app->authManager->checkAccess($userId, Yii::getAlias('@systemPermission'))){
                $dependencySpotSql = 'select count(1) from '.Spot::tableName().' where parent_spot = '.$this->parentSpotId.' and status = 1';
            }else{
                if(!self::$staticParentSpotId){
                    header('location: '.Yii::$app->urlManager->createAbsoluteUrl(Yii::getAlias('@userIndexLogout')));
//                     $this->redirect(Url::to(['@userIndexLogout']));
                    return;
                }
                $dependencySpotSql = 'select count(1) from '.Spot::tableName().' as a left join '.UserSpot::tableName().' as b on a.id = b.spot_id where a.parent_spot = '.$this->parentSpotId.' and a.status = 1 and b.user_id = '.$userId;
                $query->addSelect('b.spot_id');
                $query->leftJoin(['b' => UserSpot::tableName()],'{{a}}.id = {{b}}.spot_id');
                $query->where(['b.user_id' => $userId]);
            }
            $query->andWhere(['a.parent_spot' => $this->parentSpotId,'a.status' => 1]);
            $query->indexBy('id');
            $spotList = $query->all();
            $dependencySpot = new \yii\caching\DbDependency([
                'sql' => $dependencySpotSql
            ]);
            $cache->set($spotListCache,$spotList,0,$dependencySpot);
        }
        return $cache->get($spotListCache);
    }
    
    public function beforeSave($insert){
        if ($this->address) {
            $address = explode('/', $this->address);
            $this->province = isset($address[0])?$address[0]:'';
            $this->city = isset($address[1])?$address[1]:'';
            $this->area = isset($address[2])?$address[2]:'';
        }
        if($this->isNewRecord){
            $this->user_id = Yii::$app->user->identity->id;
        }
        return parent::beforeSave($insert);
    }
    
    public static function getSpotList($where = []){
        return self::find()->select(['id','spot_name'])->where(['parent_spot' => self::$staticParentSpotId])->andWhere($where)->asArray()->all();
    }

}
