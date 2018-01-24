<?php

namespace app\modules\patient\models;

use Yii;
use app\common\base\BaseActiveRecord;

/**
 * This is the model class for table "{{%patient_family}}".
 *
 * @property string $id
 * @property string $patient_id
 * @property integer $relation
 * @property string $name
 * @property integer $sex
 * @property string $birthday
 * @property string $iphone
 */
class PatientFamily extends BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%patient_family}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['patient_id', 'name', 'iphone', 'sex', 'relation'], 'required'],
            [['patient_id', 'relation', 'sex'], 'integer'],
            [['birthday'],'date','max' => date('Y-m-d')],
            [['name'], 'string', 'max' => 64],
            [['iphone'], 'string', 'max' => 11],
            ['iphone', 'match', 'pattern' => '/^\d{11}$/'],
            ['card', 'match', 'pattern' => '/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'patient_id' => 'Patient ID',
            'relation' => '成员关系',
            'name' => '姓名',
            'sex' => '性别',
            'birthday' => '出生日期',
            'iphone' => '手机号',
            'card' => '身份证',
        ];
    }
    public function beforeSave($insert) {

        $this->birthday = strtotime($this->birthday);
        return parent::beforeSave($insert);
    }

}
