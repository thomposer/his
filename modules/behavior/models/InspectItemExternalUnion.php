<?php

namespace app\modules\behavior\models;

use Yii;

/**
 * This is the model class for table "{{%inspect_item_external_union}}".
 *
 * @property string $id
 * @property string $company_id
 * @property string $inspect_item_id
 * @property string $external_id
 * @property string $create_time
 * @property string $update_time
 */
class InspectItemExternalUnion extends \yii\db\ActiveRecord
{
    
    private static $database;

    public function init() {
        parent::init();
        self::resetDataBase();
    }
    public function behaviors()
    {
        return [
            'bedezign\yii2\audit\AuditTrailBehavior'
        ];
    }

    private static function resetDataBase() {
        $hostInfo = Yii::$app->request->hostInfo;
        if (strpos($hostInfo, 'beta')) {
            self::$database = 'd_easyhin_his_beta_record';
        } else {
            self::$database = 'd_easyhin_his_record';
        }
    }
    
    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('recordDb');
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        if (self::$database) {
            return self::$database . '.{{%inspect_item_external_union}}';
        } else {
            return '{{%inspect_item_external_union}}';
        }
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['company_id', 'inspect_item_id'], 'required'],
            [['company_id', 'inspect_item_id', 'create_time', 'update_time'], 'integer'],
            [['external_id'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => '自增id',
            'company_id' => '0-迪安',
            'inspect_item_id' => '检验项目id',
            'external_id' => '外部项目id',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
}
