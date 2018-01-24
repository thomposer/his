<?php

namespace app\modules\rbac\models;

use Yii;

/**
 * This is the model class for table "{{%auth_item_child}}".
 *
 * @property string $parent
 * @property string $child
 * @property string $values
 *
 * @property AuthItem $parent0
 * @property AuthItem $child0
 */
class ItemChildForm extends \app\common\Base\BaseActiveRecord
{
    
    public static function tableName(){
        
        return '{{%auth_item_child}}';
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent', 'child'], 'required'],
            [['parent', 'child'], 'string', 'max' => 64],
          ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'parent' => 'Parent',
            'child' => 'Child',
            
        ];
    }

}
