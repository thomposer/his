<?php

namespace app\modules\rbac\models;

use Yii;

/**
 * This is the model class for table "{{%auth_item}}".
 *
 * @property string $name
 * @property integer $type
 * @property string $description
 * @property string $rule_name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 */
class Item extends \yii\db\ActiveRecord
{
    public $child;
    public $category;
    public $parentName;

    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auth_item}}';
    }

/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'type','description','category'], 'required'],
            [['name', 'type','parentName','category','description'], 'trim'],
            [['type', 'created_at', 'updated_at'], 'integer'],
            [['description', 'data','category','parentName'], 'string'],
            [['name', 'rule_name'], 'string', 'max' => 64,'min' => 4, 'tooLong' => '请输入长度为4-64个字符','tooShort' => '请输入长度为4-64个字符'],
            [['child'],'safe']
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => 'Name',
            'type' => 'Type',
            'description' => 'Description',
            'rule_name' => 'Rule Name',
            'data' => 'Data',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }
}
