<?php

namespace app\modules\outpatient\models;

use Yii;

/**
 * This is the model class for table "{{%recipe_type_template}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $user_id
 * @property string $name
 * @property integer $type
 * @property string $create_time
 * @property string $update_time
 */
class RecipeTypeTemplate extends \app\common\base\BaseActiveRecord
{
    public $user_name;

    public  function init()
    {
        parent::init();
        $this->spot_id=$this->spotId;//修改为诊所id

    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%recipe_type_template}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id','name'], 'required'],
            [['spot_id', 'user_id', 'type', 'create_time', 'update_time'], 'integer'],
            [['name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'spot_id' => '诊所id',
            'user_id' => '用户id',
            'name' => '模板分类',
            'type' => '处方模板分类类型，1-通用，2-个人',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
            'user_name' => '创建人',
        ];
    }
    /**
     * @desc 返回处方模板分类信息
     * @param string $fields 字段
     * @param array $where 查询条件
     * @return \yii\db\ActiveRecord[]
     */
    public static function getList($fields = '*',$where = []){
        return self::find()->select($fields)->where(['spot_id' => self::$staticSpotId])->andFilterWhere($where)->asArray()->all();
    }
    public function beforeSave($insert) {
        if($this->isNewRecord){
            $this->user_id = $this->userInfo->id;
        }
        return parent::beforeSave($insert);
    }
}
