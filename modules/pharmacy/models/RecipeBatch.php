<?php

namespace app\modules\pharmacy\models;

use Yii;

/**
 * This is the model class for table "{{%recipe_batch}}".
 *
 * @property string $id
 * @property string $recipe_record_id
 * @property string $record_id
 * @property string $spot_id
 * @property string $stock_info_id
 * @property integer $num
 * @property string $create_time
 * @property string $update_time
 */
class RecipeBatch extends \app\common\base\BaseActiveRecord
{
    public function init(){
        
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%recipe_batch}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['recipe_record_id', 'record_id', 'spot_id', 'stock_info_id', 'num', 'create_time', 'update_time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'recipe_record_id' => '发药的ID',
            'record_id' => '流水ID',
            'spot_id' => '诊所ID',
            'stock_info_id' => '入库ID(批次ID)',
            'num' => '数量',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }

    /**
     * @return 保存发药的批次和库存信息
     */
    public static function saveInfo($recipeReocrdId, $recordId, $stockInfoId, $num) {
        $model = new RecipeBatch();
        $model->recipe_record_id = $recipeReocrdId;
        $model->record_id = $recordId;
        $model->spot_id = self::$staticSpotId;
        $model->stock_info_id = $stockInfoId;
        $model->num = $num;
        $model->save();
    }

}
