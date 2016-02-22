<?php

namespace app\modules\rbac\models;

use Yii;
use app\common\base\BaseModel;

/**
 * This is the model class for table "{{%auth_rule}}".
 *
 * @property string $name
 * @property string $data
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property AuthItem[] $authItems
 */
class RuleForm extends BaseModel
{
    public $name;
    public $data;
    public $created_at;
    public $updated_at;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['data'], 'string'],
            [['created_at', 'updated_at'], 'integer'],
            [['name'], 'string', 'max' => 64]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'name' => '站点规则(站点简称)',
            'data' => '规则简称',
            'created_at' => '创建时间',
            'updated_at' => '更新时间',
        ];
    }

}
