<?php

namespace app\modules\medicine\models;

use Yii;
use app\modules\spot\models\Spot;
/**
 * This is the model class for table "{{%medicine_description}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property string $chinese_name
 * @property string $english_name
 * @property integer $create_time
 * @property integer $update_time
 *
 * @property Spot $spot
 * @property MedicineItem[] $medicineItems
 */
class MedicineDescription extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%medicine_description}}';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['chinese_name', 'english_name'], 'required'],
            [['create_time', 'update_time'], 'integer'],
            [['chinese_name', 'english_name'], 'string'],
            [['chinese_name','english_name'],'validateUnique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '编号',
            'chinese_name' => '中文通用名',
            'english_name' => '英文通用名',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    /**
     * @return 验证中文通用名和英文通用名的唯一性
     * @param unknown $attribute
     * @param unknown $params
     */
    public function validateUnique($attribute,$params){
        if(!$this->hasErrors()){
            if($this->isNewRecord){
                $record = self::find()->select(['id'])->where(['chinese_name' => $this->chinese_name,'english_name' => $this->english_name])->asArray()->one();
                if($record){
                    $this->addError('chinese_name','中文通用名和英文通用名已经被占用了。');
                }
            }
        }
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMedicineItems()
    {
        return $this->hasMany(MedicineItem::className(), ['medicine_description_id' => 'id']);
    }
    /**
     * @return 返回当前机构下所有的用药指南列表
     */
    public static function getList() {
         return self::find()->select(['id','chinese_name','english_name'])->asArray()->all();
    }
}
