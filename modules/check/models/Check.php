<?php

namespace app\modules\check\models;

use app\modules\outpatient\models\CheckRecord;
use Yii;
use yii\db\Query;
use app\modules\user\models\User;

/**
 * This is the model class for table "{{%check_record}}".
 * @abstract 辅助(影像学)检查
 * @property integer $id
 * @property integer $record_id
 * @property integer $spot_id
 * @property string $name
 * @property string $unit
 * @property string $price
 * @property integer $status
 * @property integer $create_time
 * @property integer $update_time
 */
class Check extends \app\modules\outpatient\models\CheckRecord
{

    public $doctorName;
    public $patientName;
    public $clinic_name;
    public $type_description;
    public $sex;
    public $birthday;
    public $check;
    public $avatar;

    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['check'], 'required', 'message' => '请选择检查项目', 'on' => 'check'],
            [['avatar'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        $arrParent = parent::attributeLabels();

        $arrExt = [
            'name' => '检查项名称',
            'type_description' => '服务类型',
            'clinic_name' => '所在诊室',
            'patientName' => '患者信息',
            'doctorName' => '接诊医生'
        ];

        return array_merge($arrParent, $arrExt);
    }

    /**
     * @return 返回影像学检查名称列表
     * @param 就诊流水id $id
     */
    public static function getCheckList($id) {
        $list = self::find()->select(['name'])->where(['spot_id' => self::$staticSpotId, 'record_id' => $id])->asArray()->all();
        $name = [];
        foreach ($list as $v) {
            $name[] = $v['name'];
        }
        return implode(',', $name);
    }

    /**
     * @return 返回状态不同的影像学检查数量
     * @param 就诊流水id $id
     * @param 状态 $status
     * @param 属性 $type 1表示在查看报告处，去掉诊所ID条件
     */
    public static function getCheckNum($id, $status = 3, $type = 0) {
        if ($type) {
            $num = self::find()->where(['record_id' => $id, 'status' => $status])->asArray()->count(1);
        } else {
            $num = self::find()->where(['spot_id' => self::$staticSpotId, 'record_id' => $id, 'status' => $status])->asArray()->count(1);
        }
        return $num;
    }
    
   /**
     * @param $dataProvider
     * @return array 获取影像学检查项的数量,根据reocrd_id和status分类
     */
    public static function getCheckNumByList($dataProvider) {
        foreach ($dataProvider->models as $model) {
            $recordIdList[] = $model->id;
        }
        $query = new Query();
        $data = $query->from(['a' => self::tableName()])
                ->select(['record_id', 'status', 'count(1) as count'])
                ->where(['record_id' => $recordIdList, 'spot_id' => self::$staticSpotId])
                ->groupBy('record_id,status')
                ->all();
        $checkStatusCount = [];
        foreach ($data as $value) {
            $checkStatusCount[$value['record_id']][$value['status']] = (int)$value['count'];
        }
        return $checkStatusCount;
    }

    /**
     * @return 返回状态不同的影像学检查数量 array
     * @param 就诊流水id $id
     * @param 状态 $status
     * @param 属性 $type 1表示在查看报告处，去掉诊所ID条件
     */
    public static function getCheckNumData($id, $status = 3, $type = 0) {
        if ($type) {
            $num = self::find()->select(['record_id', 'id'])->where(['record_id' => $id, 'status' => $status])->indexBy('record_id')->asArray()->all();
        } else {
            $num = self::find()->select(['record_id', 'id'])->where(['spot_id' => self::$staticSpotId, 'record_id' => $id, 'status' => $status])->indexBy('record_id')->asArray()->all();
        }
        return $num;
    }

    /**
     * @param $record_id
     * @param int $status
     * @return mixed 返回影像学检查列表信息
     */
    public static function getCheckListByRecord($record_id, $status = 3) {
        $field = ['a.id', 'a.name', 'a.description', 'a.result', 'a.status', 'file_id' => 'group_concat(c.id)', 'file_url' => 'group_concat(c.file_url)', 'file_name' => 'group_concat(c.file_name)', 'size' => 'group_concat(c.size)'];
        $query = new Query();
        $query->from(['a' => Check::tableName()]);
        $query->select($field);
        if ($status == 1 || is_array($status) && in_array('1', $status)) {
            $query->addSelect(['a.report_time', 'b.username', 'b.iphone', 'b.sex', 'b.birthday']);
            $query->leftJoin(['b' => User::tableName()], '{{a}}.report_user_id = {{b}}.id');
        }
        $query->leftJoin(['c' => CheckRecordFile::tableName()], '{{a}}.id = {{c}}.check_record_id');
        $query->where(['a.record_id' => $record_id, 'a.status' => $status]);
        $query->groupBy('a.id');
        $list = $query->all();
        return $list;
    }

    /**
     * @param $id
     * @return int|string  通过ID查询
     */
    public static function getCheckNumById($id) {
        return self::find()->where(['spot_id' => self::$staticSpotId, 'id' => $id])->asArray()->count();
    }

    /**
     * @param $record_id 流水id
     * @param string $fields 查询字段
     * @return array|\yii\db\ActiveRecord[] 获取影像学检查项的项目
     */
    public static function getCheckItemList($record_id, $fields = '*') {
        $checkItemList = CheckRecord::find()->select($fields)->where(['record_id' => $record_id, 'spot_id' => self::$staticSpotId])->asArray()->all();
        return $checkItemList;
    }

}
