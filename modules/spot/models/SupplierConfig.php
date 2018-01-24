<?php

namespace app\modules\spot\models;

use Yii;

/**
 * 供应商配置
 * This is the model class for table "{{%supplier_conf}}".
 *
 * @property integer $id
 * @property string $spot_id
 * @property string $name
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class SupplierConfig extends \app\common\base\BaseActiveRecord
{
    public function init(){
        
        parent::init();
        $this->spot_id = $this->parentSpotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%supplier_config}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name','spot_id'],'required'],
            [['spot_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['name'], 'validateSupplierName'],
            [['name'], 'trim'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '机构ID',
            'name' => '供应商名称',
            'status' => '状态',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    /**
     * @property 获取status为1的列表
     * 
     */
    public static function getList(){
       return self::find()->select(['id','name'])->where(['spot_id' => self::$staticParentSpotId,'status' => 1])->asArray()->all();
    }

    public function validateSupplierName($attribute){
        $parentSpotId = $this->parentSpotId;
        if ($this->isNewRecord) {
            $hasRecord = SupplierConfig::find()->select(['id'])->where(['spot_id' => $parentSpotId, $attribute => $this->$attribute])->asArray()->limit(1)->one();
            if ($hasRecord) {
                $this->addError($attribute, '该供应商名称已存在');
            }
        } else {
            $oldSupplierName = $this->getOldAttribute('name');
            if ($oldSupplierName != $this->name) {
                $hasRecord = $this->checkDuplicate('name', $this->name);
                if ($hasRecord) {
                    $this->addError('name',   '该供应商名称已存在');
                }
            }
        }
    }
    protected function checkDuplicate($attribute, $params) {
        $parentSpotId = $this->parentSpotId;
        $hasRecord = SupplierConfig::find()->select(['id'])->where(['spot_id' => $parentSpotId, $attribute => $params])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }
}
