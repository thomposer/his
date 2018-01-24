<?php

namespace app\modules\medicine\models;

use Yii;

/**
 * This is the model class for table "{{%medicine_item}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property integer $medicine_description_id
 * @property string $indication
 * @property string $used
 * @property string $renal_description
 * @property string $liver_description
 * @property string $contraindication
 * @property string $side_effect
 * @property string $pregnant_woman
 * @property string $breast
 * @property string $careful
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property MedicineDescription $medicineDescription
 */
class MedicineItem extends \app\common\base\BaseActiveRecord
{
    public $chinese_name;//中文通用名
    public $english_name;//英文通用名
    public $indicationSelect;//使用指征select属性
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%medicine_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['medicine_description_id','indication'], 'required'],
            [['medicine_description_id', 'create_time', 'update_time'], 'integer'],
            [['used', 'renal_description', 'liver_description', 'contraindication', 'side_effect', 'pregnant_woman', 'breast', 'careful'], 'string'],
            [['indication'], 'string', 'max' => 255],
            [['medicine_description_id','indication'],'validateUnique'],
            [['medicine_description_id'], 'exist', 'skipOnError' => true, 'targetClass' => MedicineDescription::className(), 'targetAttribute' => ['medicine_description_id' => 'id']],
            [['english_name','chinese_name','indicationSelect'],'safe']
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'medicine_description_id' => '用药指南表id',
            'chinese_name' => '中文通用名',
            'english_name' => '英文通用名',
            'indication' => '使用指征',
            'indicationSelect' => '使用指征',
            'used' => '使用方法',
            'renal_description' => '肾功能不全',
            'liver_description' => '肝功能不全',
            'contraindication' => '禁忌症',
            'side_effect' => '常见副作用',
            'pregnant_woman' => '孕妇',
            'breast' => '母乳',
            'careful' => '注意事项',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedicineDescription()
    {
        return $this->hasOne(MedicineDescription::className(), ['id' => 'medicine_description_id']);
    }
    
    /**
     * @return 新增时，验证用药指南表id和使用指征的联合唯一性
     * @param 字段属性名 $attribute
     * @param unknown $params
     */
    public function validateUnique($attribute,$params){
        if(!$this->hasErrors()){
            if($this->isNewRecord){
                $hasRecord = self::find()->select(['id'])->where(['medicine_description_id' => $this->medicine_description_id,'indication' => $this->indication])->asArray()->one();
                if($hasRecord){
                    $this->addError('indication','该使用指征已经被占用了。');
                }
            }
        }    
    }
}
