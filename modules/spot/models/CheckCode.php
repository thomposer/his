<?php

namespace app\modules\spot\models;

use Yii;

/**
 * This is the model class for table "{{%check_code}}".
 *
 * @property integer $id
 * @property integer $spot_id
 * @property string $name
 * @property string $major_code
 * @property string $add_code
 * @property string $help_code
 * @property integer $status
 * @property string $remark
 * @property integer $create_time
 * @property integer $update_time
 */
class CheckCode extends \app\common\base\BaseActiveRecord
{

    public function init() {
        parent::init();
        $this->spot_id = $this->parentSpotId;
    }

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return '{{%check_code}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'status', 'name'], 'required'],
            [['spot_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['major_code'], 'validateIsEmpty', 'skipOnEmpty' => false],
            [['major_code'], 'validateMajorCode'],
            [['add_code'], 'validateAddCode'],
            [['name'], 'validateName'],
            [['name', 'major_code', 'add_code', 'help_code'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 64],
            [['major_code', 'add_code', 'name', 'help_code', 'remark'], 'default', 'value' => ''],
            [['status'], 'default', 'value' => 1]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => '序号',
            'spot_id' => 'Spot ID',
            'name' => '疾病名称',
            'major_code' => '主要编码',
            'add_code' => '附加编码',
            'help_code' => '助记码',
            'status' => '状态',
            'remark' => '备注',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * function 获取助记码状态
     * @var array
     */
    static $getStatus = [
        1 => "正常",
        2 => "停用"
    ];

    public function validateIsEmpty($attribute) {
        if (!$this->major_code && !$this->add_code) {
            return $this->addError('major_code', '主要编码和附加编码至少填写一个');
        }
    }

    /**
     * @function 判断主要编码是否唯一
     * @param $attribute
     */
    public function validateMajorCode($attribute) {
        if ($this->isNewRecord) {
            $hasRecord = self::find()->select(['id'])->where(['major_code' => $this->major_code, 'spot_id' => $this->spot_id])->asArray()->one();
            if ($hasRecord) {
                $this->addError($attribute, '主要编码已存在');
            }
        } else {
            $oldShiftName = $this->getOldAttribute('major_code');
            if ($oldShiftName != $this->major_code) {
                $hasRecord = $this->checkDuplicate('major_code', $this->major_code);
                if ($hasRecord) {
                    $this->addError('major_code', '主要编码已存在');
                }
            }
        }
    }

    /**
     * @function 判断疾病名称是否唯一
     * @param $attribute
     */
    public function validateName($attribute) {
        if ($this->isNewRecord) {
            $hasRecord = self::find()->select(['id'])->where(['name' => trim($this->name), 'spot_id' => $this->spot_id])->asArray()->one();
            if ($hasRecord) {
                $this->addError($attribute, '疾病名称已存在');
            }
        } else {
            $oldShiftName = $this->getOldAttribute('name');
            if ($oldShiftName != $this->name) {
                $hasRecord = $this->checkDuplicate('name', trim($this->name));
                if ($hasRecord) {
                    $this->addError('name', '疾病名称已存在');
                }
            }
        }
    }

    /**
     * @function 判断附加编码是否唯一
     * @param $attribute
     */
    public function validateAddCode($attribute) {

        if ($this->isNewRecord) {
            $hasRecord = self::find()->select(['id'])->where(['add_code' => trim($this->add_code), 'spot_id' => $this->spot_id])->asArray()->one();
            if ($hasRecord) {
                $this->addError($attribute, '附加编码已存在');
            }
        } else {
            $oldShiftName = $this->getOldAttribute('add_code');
            if ($oldShiftName != $this->add_code) {
                $hasRecord = $this->checkDuplicate('add_code', $this->add_code);
                if ($hasRecord) {
                    $this->addError('add_code', '附加编码已存在');
                }
            }
        }
    }

    /**
     * @function 验证唯一性
     * @author JeanneWu
     * @time 2017年7月12日 14:46
     */
    protected function checkDuplicate($attribute, $params) {
        $hasRecord = self::find()->select(['id'])->where([$attribute => $this->$attribute, 'spot_id' => $this->spot_id])->asArray()->one();
        if ($hasRecord) {
            return true;
        } else {
            return false;
        }
    }

    static public function getData() {
        $id = YII_DEBUG ? 19292 : 41834;
        return self::find()->select(['id', 'name', 'help_code', 'major_code'])->where(['spot_id' => self::$staticParentSpotId, 'status' => 1, 'id' => $id])->asArray()->one();
    }

}
