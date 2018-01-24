<?php

namespace app\modules\outpatient\models;

use app\modules\spot\models\CheckList;
use Yii;
use app\modules\spot_set\models\CheckListClinic;
use yii\db\Exception;
use yii\db\Query;

/**
 * This is the model class for table "{{%check_template_info}}".
 *
 * @property string $id
 * @property string $spot_id
 * @property string $clinic_check_id
 * @property string $check_template_id
 * @property string $create_time
 * @property string $update_time
 *
 * @property CheckTemplate $checkTemplate
 * @property ChecklistClinic $clinicCheck
 */
class CheckTemplateInfo extends \app\common\base\BaseActiveRecord
{
    /**
     * @inheritdoc
     */

    public $checkName;
    public $deleted;

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }


    public static function tableName()
    {
        return '{{%check_template_info}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['spot_id', 'check_template_id'], 'integer'],
            ['clinic_check_id', 'validateCheckId'],
            [['deleted'], 'safe'],
        ];
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spot_id' => '诊所id',
            'clinic_check_id' => '诊所检查医嘱id',
            'check_template_id' => '检查模版配置id',
            'checkName' => '选择检查医嘱',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCheckTemplate()
    {
        return $this->hasOne(CheckTemplate::className(), ['id' => 'check_template_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClinicCheck()
    {
        return $this->hasOne(ChecklistClinic::className(), ['id' => 'clinic_check_id']);
    }

    /**
     * @param $attribute
     * @param $params
     * @return 验证是否有选择检查医嘱
     */
    public function validateCheckId($attribute, $params) {
        if (count($this->clinic_check_id) > 0) {
            foreach ($this->clinic_check_id as $key => $v) {
                try {
                    $list = json_decode($v, true);
                } catch (Exception $e) {
                    $this->addError($attribute, '参数错误');
                }
                if (!preg_match("/^\s*[+-]?\d+\s*$/", $list['id'])) {
                    $this->addError($attribute, '参数错误');
                }
            }
        }
    }

    /**
     *
     * @param type $model CheckTemplateModel
     * @param type $checkTemplateInfoModel
     * @return 插入checkTemplateInfoModel的关联信息
     */
    public static function saveInfo($model, $checkTemplateInfoModel) {
        $result = $model->getModel('checkTemplate')->save();
        if ($result) {
            $rows = [];
            foreach ($checkTemplateInfoModel->clinic_check_id as $key => $v) {
                if ($checkTemplateInfoModel->deleted[$key] == null) {
                    $info = json_decode($v, true);
                    $rows[] = [
                        self::$staticSpotId,
                        $info['id'],
                        $model->getModel('checkTemplate')->id,
                        time(),
                        time()
                    ];
                }
            }
            if (count($rows) > 0) {
                Yii::$app->db->createCommand()->batchInsert(CheckTemplateInfo::tableName(), [
                    'spot_id', 'clinic_check_id', 'check_template_id', 'create_time', 'update_time'
                ], $rows)->execute();
            }
            if (array_sum($checkTemplateInfoModel->deleted) == count($checkTemplateInfoModel->clinic_check_id)) {//全部是删除
                return false;
            } else {
                return true;
            }
        }
    }

    /**
     * @param int $id 检查医嘱模板id
     * @return 获取检查医嘱模板详情
     */
    public static function findCheckTemplateInfoDataProvider($id) {
        $data = (new Query())->from(['a' => CheckTemplateInfo::tableName()])
            ->select(['a.id', 'name' => 'c.name', 'a.clinic_check_id'])
            ->leftJoin(['b' => CheckListClinic::tableName()], '{{a}}.clinic_check_id={{b}}.id')
            ->leftJoin(['c' => CheckList::tableName()], '{{b}}.check_id={{c}}.id')
            ->where(['a.check_template_id' => $id, 'a.spot_id' => self::$staticSpotId])
            ->orderBy(['a.id' => SORT_ASC])->all();
        return $data;
    }


    /**
     * @param $model
     * @param $checkTemplateInfoModel
     * @return bool
     * @desc 检查医嘱模板修改
     */
    public static function updateInfo($model, $checkTemplateInfoModel) {
        $result = $model->getModel('checkTemplate')->save();
        if ($result) {
            $rows = [];
            foreach ($checkTemplateInfoModel->clinic_check_id as $key => $v) {
                $list = json_decode($v, true);
                if (isset($list['isNewRecord']) && $list['isNewRecord'] === 0) {
                    if ($checkTemplateInfoModel->deleted[$key] == 1) {
                        Yii::$app->db->createCommand()->delete(CheckTemplateInfo::tableName(), ['id' => $list['id'], 'spot_id' => self::$staticSpotId])->execute();
                    }
                } else {
                    if ($checkTemplateInfoModel->deleted[$key] == null) {
                        $rows[] = [
                            self::$staticSpotId,
                            $list['id'],
                            $model->getModel('checkTemplate')->id,
                            time(),
                            time()
                        ];
                    }
                }
            }
            if (count($rows) > 0) {
                Yii::$app->db->createCommand()->batchInsert(CheckTemplateInfo::tableName(), [
                    'spot_id', 'clinic_check_id', 'check_template_id', 'create_time', 'update_time'
                ], $rows)->execute();
            }
            if (array_sum($checkTemplateInfoModel->deleted) == count($checkTemplateInfoModel->clinic_check_id)) {//全部是删除
                return false;
            } else {
                return true;
            }
        }
    }

}
