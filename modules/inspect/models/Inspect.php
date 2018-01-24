<?php

namespace app\modules\inspect\models;

use app\modules\outpatient\models\InspectRecord;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use app\modules\report\models\Report;
use app\modules\spot\models\Inspect as InspectConfig;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\User;
use yii\db\ActiveRecord;
use yii\db\Query;

/**
 * This is the model class for table "{{%inspect_record}}".
 *
 * @property string $id
 * @property string $record_id
 * @property string $spot_id
 * @property string $name
 * @property string $unit
 * @property string $price
 * @property integer $status
 * @property string $type_description 预约服务类型
 * @property string $inspect_type 检验类型
 * @property integer $deliver 标本是否外送检验
 * @property integer $specimen_type 标本种类
 * @property integer $cuvette 试管盖颜色
 * @property string  $specimen_number 标本编码
 * @property integer $notice_status 通知医生状态(1-已通知，2-未通知)	
 * @property integer $notice_user_id 通知医生--操作人	
 * @property integer $notice_time 点击通知医生-时间	
 * @property integer $handle_status 医生处理状态(1-已处理，2-未处理)	
 * @property integer $handle_time 医生点击处理的时间
 * @property string $create_time
 * @property string $update_time
 */
class Inspect extends InspectRecord
{

    public $username; //患者信息
    public $birthday; //出生日期
    public $sex; //性别
    public $room_name; //所在诊室
    public $inspect_name; //检验项名称
    public $doctor_name; //开单医生
    public $type_description; //就诊类型
    public $status; //检查项状态
    public $onInspect; //检查中状态
    public $pain_score; //疼痛评分
    public $fall_score; //跌倒评分
    public $patient_number; //病例号

    /**
     * @inheritdoc
     */

    public static function tableName() {
        return '{{%inspect_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['onInspect'], 'required', 'on' => 'on-inspect'],
            [['onInspect'], 'validateOnInspect', 'on' => 'on-inspect'],
            [['record_id', 'spot_id', 'status', 'inspect_in_time', 'inspect_finish_time', 'notice_status', 'notice_user_id', 'notice_time', 'handle_status', 'handle_time', 'create_time', 'update_time'], 'integer'],
            [['price'], 'number'],
            [['name', 'unit', 'inspect_type'], 'string', 'max' => 64],
            [['specimen_number'], 'string', 'max' => 32],
            [['inspect_type', 'specimen_number'], 'default', 'value' => ''],
            [['notice_status', 'notice_user_id', 'notice_time', 'handle_status', 'handle_time'], 'default', 'value' => 0],
            [['deliver'], 'default', 'value' => 2],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        $parent_f = parent::attributeLabels();

        $parent_t = [
            'id' => 'ID',
            'username' => '患者信息',
            'room_name' => '所在诊室',
            'inspect_name' => '检验项名称',
            'doctor_name' => '接诊医生',
            'type_description' => '服务类型',
            'status' => '检查项状态',
            'onInspect' => '检查项目',
            'deliver' => '标本是否外送检验',
            'specimen_type' => '标本种类',
            'cuvette' => '试管盖颜色',
            'inspect_type' => '检验类型',
            'specimen_number' => '标本编码',
            'patient_number' => '病历号',
            'notice_status' => '通知医生状态(1-已通知，2-未通知)	',
            'notice_user_id' => '通知医生--操作人',
            'notice_time' => '点击通知医生-时间	',
            'handle_status' => '医生处理状态(1-已处理，2-未处理)	',
            'handle_time' => '医生点击处理的时间',
        ];

        $arr = array_merge($parent_f, $parent_t);
        return $arr;
    }

    public function validateOnInspect($attribute, $params) {

        if ($this->scenario == 'on-inspect') {
            $list = self::getInspectRecordList(['id' => $this->onInspect], ['deliver', 'specimen_type', 'cuvette', 'inspect_type']);
            foreach ($list as $v) {
                
            }
        }
    }

    /**
     * @param $record_id
     * @param int $status
     * @return int|string 获取实验室检查项的数量
     * @param 属性 $type 1表示在查看报告处，去掉诊所ID条件
     */
    public static function getInspectNum($record_id, $status = 3, $type = 0) {
        if ($type) {
            $num = self::find()->where(['record_id' => $record_id, 'status' => $status])->asArray()->count(1);
        } else {
            $num = self::find()->where(['record_id' => $record_id, 'status' => $status, 'spot_id' => self::$staticSpotId])->asArray()->count(1);
        }
        return $num;
    }

    /**
     * @param $dataProvider
     * @return array 获取实验室检查项的数量,根据reocrd_id和status分类
     */
    public static function getInspectNumByList($dataProvider) {
        foreach ($dataProvider->models as $model) {
            $recordIdList[] = $model->record_id;
        }
        $query = new Query();
        $data = $query->from(['a' => self::tableName()])
                ->select(['record_id', 'status', 'count(1) as count'])
                ->where(['record_id' => $recordIdList, 'spot_id' => self::$staticSpotId])
                ->groupBy('record_id,status')
                ->all();
        $inspectStatusCount = [];
        foreach ($data as $value) {
            $inspectStatusCount[$value['record_id']][$value['status']] = (int) $value['count'];
        }
        return $inspectStatusCount;
    }

    /**
     * @param $record_id
     * @param int $status
     * @return array 获取实验室检查项的数量数组
     * @param 属性 $type 1表示在查看报告处，去掉诊所ID条件
     */
    public static function getInspectNumData($record_id, $status = 3, $type = 0) {
        if ($type) {
            $num = self::find()->select(['record_id', 'id'])->where(['record_id' => $record_id, 'status' => $status])->indexBy('record_id')->asArray()->all();
        } else {
            $num = self::find()->select(['record_id', 'id'])->where(['record_id' => $record_id, 'status' => $status, 'spot_id' => self::$staticSpotId])->indexBy('record_id')->asArray()->all();
        }
        return $num;
    }

    /**
     * @param $record_id  流水ID
     * @param $status 状态
     * @param $spot 1|2 1/区分诊所 2/不区分诊所
     * @return array|ActiveRecord[] 获取实验室检查项的列表需要区分诊所
     */
    public static function getInspectListByRecord($record_id, $status = 3, $spot = 1) {
        $field = ['a.id', 'a.specimen_number', 'a.deliver', 'a.name', 'a.status', 'file_id' => 'group_concat(c.id)', 'file_url' => 'group_concat(c.file_url)', 'file_name' => 'group_concat(c.file_name)', 'size' => 'group_concat(c.size)'];
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select($field);

        if ($status == 1 || is_array($status) && in_array('1', $status)) {
            $query->addSelect(['a.report_time', 'b.username', 'b.iphone', 'b.sex', 'b.birthday']);
            $query->leftJoin(['b' => User::tableName()], '{{a}}.report_user_id = {{b}}.id');
        }
        $query->leftJoin(['c' => InspectRecordFile::tableName()], '{{a}}.id = {{c}}.inspect_record_id');
        $query->where([ 'a.record_id' => $record_id, 'a.status' => $status]);
        if ($spot == 1) {
            $query->andWhere(['a.spot_id' => self::$staticSpotId]);
        }
        $query->groupBy('a.id');
        $list = $query->all();
        return $list;
    }

    /**
     * @param $where 查询条件
     * @return array|ActiveRecord[] 获取实验室检查项的项目
     */
    public static function getInspectRecordList($where, $fields = '*') {
        $inspectItemList = InspectRecord::find()->select($fields)->where(['spot_id' => self::$staticSpotId])->andWhere($where)->asArray()->all();
        return $inspectItemList;
    }

    public static $getPatientsType = [
        1 => '初诊',
        2 => '复诊',
    ];

    /**
     * 
     * @return string 生成标本编码
     */
    public static function generateSpecimenNumber() {
        $sn = substr(time(), -6) . substr(microtime(), 2, 3) . sprintf('%03d', rand(0, 999));
        return $sn;
    }

    /**
     * 
     * @param int/array $inspectId  实验室检查ID 
     * @return array 根据实验室检查ID获取相应的标本 
     */
    public static function getSpecimenByInspect($inspectId) {
        $query = new Query();
        $data = $query->from(['a' => self::tableName()])
                ->select(['a.id', 'a.inspect_in_time', 'a.deliver', 'inspectName' => 'GROUP_CONCAT(a.name)', 'a.specimen_type', 'a.cuvette', 'a.inspect_type', 'a.specimen_number', 'c.username', 'c.sex', 'c.birthday', 'departmentName' => 'e.name'])
                ->leftJoin(['b' => PatientRecord::tableName()], '{{a}}.record_id={{b}}.id')
                ->leftJoin(['c' => Patient::tableName()], '{{b}}.patient_id={{c}}.id')
                ->leftJoin(['d' => Report::tableName()], '{{d}}.record_id={{a}}.record_id')
                ->leftJoin(['e' => SecondDepartment::tableName()], '{{d}}.second_department_id={{e}}.id')
                ->where(['a.id' => $inspectId])
                ->groupBy(['a.specimen_number'])
                ->indexBy('specimen_number')
                ->all();
        foreach ($data as &$val) {
            $val['inspect_in_time'] = $val['inspect_in_time'] ? date("Y-m-d H:i", $val['inspect_in_time']) : '';
            $val['sex'] = Patient::$getSex[$val['sex']];
            $val['birthday'] = Patient::dateDiffage($val['birthday']);
            $val['specimen_type'] = InspectConfig::$getSpecimenType[$val['specimen_type']];
            $val['cuvette'] = InspectConfig::$getCuvette[$val['cuvette']];
            $val['inspectName'] = $val['inspectName'] . ($val['deliver'] == 1 ? '(外送)' : '');
        }
        return $data;
    }

    /**
     * 
     * @param type $recordId 流水ID
     * @return  获取是否有需要提醒的项目
     */
    public static function getWarning($recordId) {
        $record = (new Query())->from(['a' => self::tableName()])
                ->select(['a.id', 'a.record_id', 'b.name', 'b.result_identification'])
                ->leftJoin(['b' => InspectRecordUnion::tableName()], '{{a}}.id={{b}}.inspect_record_id')
                ->where(['a.notice_status' => 2, 'a.spot_id' => self::$staticSpotId, 'a.record_id' => $recordId])
                ->andWhere([
                    'or',
                    ['b.result_identification' => 'HH'],
                    ['b.result_identification' => 'LL']
                ])
                ->all();
        $data = [];
        if (!empty($record)) {
            foreach ($record as $val) {
                if (!isset($data[$val['id']])) {
                    $data[$val['id']] = '【' . $val['name'] . '】';
                } else {
                    $data[$val['id']] = $data[$val['id']] . '、' . '【' . $val['name'] . '】';
                }
            }
        }
        return $data;
    }

    /**
     * 
     * @param type $id
     * @return type 获取InspectModel
     * @throws NotFoundHttpException
     */
    public static function findModel($id) {
        if (($model = Inspect::findOne(['id' => $id, 'spot_id' => self::$staticSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

}
