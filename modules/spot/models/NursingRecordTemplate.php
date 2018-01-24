<?php

namespace app\modules\spot\models;

use Yii;

/**
 * This is the model class for table "{{%nursing_record_template}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $operating_id
 * @property string $nursing_item
 * @property string $content_template
 * @property string $create_time
 * @property string $update_time
 * * @property string $username
 */
class NursingRecordTemplate extends \app\common\base\BaseActiveRecord
{
    public $username;  // 创建人

    public function init() {

        parent::init();
        $this->spot_id = $this->parentSpotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%nursing_record_template}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spot_id', 'content_template','nursing_item'], 'required'],
            [['spot_id', 'operating_id', 'create_time', 'update_time'], 'integer'],
            [['content_template'], 'string', 'max' => 1000],
            [['nursing_item'], 'string', 'max' => 64],
            ['nursing_item','validateNursingItem']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '机构ID',
            'operating_id' => '操作人ID',
            'username' => '创建人',
            'nursing_item' => '护理项',
            'content_template' => '内容模板',
            'create_time' => '创建时间',
            'update_time' => 'Update Time',
        ];
    }

    public function beforeSave($insert) {
        if ($this->isNewRecord) {
            $this->operating_id = $this->userInfo->id;  // 创建人
        }
        return parent::beforeSave($insert);
    }

    public function validateNursingItem($attribute, $params) {
        if ($this->isNewRecord) {
            if ($this->checkDuplicate($this->$attribute)) {
                $this->addError($attribute, '保存失败，已存在同名的护理项');
            }
        }else{
            $oldAttribute = $this->getOldAttribute($attribute);
            if ($oldAttribute != $this->$attribute) {
                $hasRecord = $this->checkDuplicate($this->$attribute);
                if ($hasRecord) {
                    $this->addError($attribute,'保存失败，已存在同名的护理项');
                }
            }
        }
    }

    protected function checkDuplicate($nursing_item) {
        $hasRecord = self::find()->select(['id'])->where(['nursing_item' => $nursing_item, 'spot_id' => $this->spot_id])->asArray()->limit(1)->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return 获取当前机构所有的护理记录模板
     */
    public static function getNursingRecordTemplate($where = null){
        if($where == null){
            $where = '1=1';
        }
        return self::find()->select(['id','name'=>'nursing_item','content'=>'content_template'])->where(['spot_id'=>self::$staticParentSpotId])->andWhere($where)->asArray()->all();
    }
}
