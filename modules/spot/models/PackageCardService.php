<?php

namespace app\modules\spot\models;

use Yii;

/**
 * This is the model class for table "gzh_package_card_service".
 *
 * @property string $id
 * @property string $parent_spot_id
 * @property string $name
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class PackageCardService extends \app\common\base\BaseActiveRecord
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
        return '{{%package_card_service}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id','name','status'], 'required'],
            [['spot_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['name'], 'string', 'max' => 20],
            ['name','validateName'],
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
            'name' => '名称',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
    public static $getStatus = [
        '1' => '正常',
        '2' => '停用'
    ];
    
    public function beforeSave($insert) {
        $this->name = trim($this->name);
        return parent::beforeSave($insert);
    }
    
    public function validateName($attribute){
        if($this->isNewRecord){
            $haveRecord = self::find()->select(['id'])->where(['spot_id' => $this->spot_id, $attribute => trim($this->$attribute)])->asArray()->limit(1)->one();
        }else{
            $haveRecord = self::find()->select(['id'])->where(['spot_id' => $this->spot_id, $attribute => trim($this->$attribute)])->andWhere(['<>', 'id',$this->id])->asArray()->limit(1)->one();  
        }
        if($haveRecord){
                $this->addError($attribute, '服务类型名称不可重复');
        }
        
    }
    
    static function getServiceList($where = ['status' => 1]){
        return self::find()->select(['id', 'name'])->where(['spot_id' => self::$staticParentSpotId])->andWhere($where)->asArray()->all();
    }
    
}
