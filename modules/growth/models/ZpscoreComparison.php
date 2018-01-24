<?php

namespace app\modules\growth\models;

use Yii;

/**
 * This is the model class for table "{{%zpscore_comparison}}".
 *
 * @property string $id
 * @property double $zscore
 * @property string $pscore
 * @property string $create_time
 * @property string $update_time
 */
class ZpscoreComparison extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%zpscore_comparison}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['zscore'], 'number'],
            [['create_time', 'update_time'], 'integer'],
            [['pscore'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'zscore' => 'Zscore',
            'pscore' => 'Pscore',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
}
