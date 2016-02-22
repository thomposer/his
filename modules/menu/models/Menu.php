<?php

namespace app\modules\menu\models;

use Yii;

/**
 * This is the model class for table "{{%menu}}".
 *
 * @property integer $id
 * @property string $menu_url
 * @property integer $type
 * @property string $description
 * @property integer $parent_id
 * @property integer $status
 * @property integer $role_type
 *
 * @property Title $parent
 */
class Menu extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_url', 'description', 'parent_id', 'role_type'], 'required'],
            [['type', 'parent_id', 'status', 'role_type'], 'integer'],
            [['menu_url', 'description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'menu_url' => 'Menu Url',
            'type' => 'Type',
            'description' => 'Description',
            'parent_id' => 'Parent ID',
            'status' => 'Status',
            'role_type' => 'Role Type',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Title::className(), ['id' => 'parent_id']);
    }
}
