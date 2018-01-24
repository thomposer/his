<?php

namespace app\modules\cure\models;

use app\modules\outpatient\models\CureRecord;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "gzh_cure_record".
 *
 * @property string $id
 * @property string $record_id
 * @property string $spot_id
 * @property string $name
 * @property string $unit
 * @property string $price
 * @property string $time
 * @property string $description
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 */
class Cure extends \app\modules\outpatient\models\CureRecord
{

    public $username; //患者信息
    public $iphone; //患者信息
    public $cure_name; //药品名称
    public $doctor_name; //开单医生
    public $type_description; //就诊类型 
    public $cure_status; //发药状态 
    public $sex; //发药状态 
    public $birthday; //发药状态 
    public $room_name; //所在诊室 
    public $cure; //治疗项目

    public function rules() {
        return [
            [['cure'], 'required', 'message' => '请选择治疗项目', 'on' => 'cure'],
        ];
    }

    public function scenarios() {
        $scenarios = parent::scenarios();
        $scenarios['cure'] = ['cure'];
        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        $parent_f = parent::attributeLabels();

        $parent_t = [
            'id' => 'ID',
            'username' => '患者信息',
            'doctor_name' => '接诊医生',
            'type_description' => '服务类型',
            'cure_status' => '检查项状态',
            'cure_name' => '治疗项目',
            'iphone' => '手机号码',
            'room_name' => '所在诊室',
        ];

        $parent = array_merge($parent_f, $parent_t);
        return $parent;
    }

    /*
     * 获取治疗项目名称 以，号隔开
     */

    public static function getCureArray($record_id) {
        $record = self::find()->select('name')->where(['record_id' => $record_id, 'spot_id' => self::$staticSpotId])->asArray()->all();
        $name = [];
        foreach ($record as $v) {
            $name[] = $v['name'];
        }
        return implode(',', $name);
    }
    
    /**
     * @param $dataProvider
     * @return array 治疗项目名称 以，号隔开,根据reocrd_id分类
     */
    public static function getCureArrayByList($dataProvider) {
        foreach ($dataProvider->models as $model) {
            $recordIdList[] = $model->record_id;
        }
        $query = new Query();
        $data = $query->from(['a' => self::tableName()])
                ->select(['record_id', 'GROUP_CONCAT(name) as name',])
                ->where(['record_id' => $recordIdList, 'spot_id' => self::$staticSpotId])
                ->groupBy('record_id')
                ->all();
        return array_column($data, 'name', 'record_id');
    }

    /*
     * 获取治疗项目的数量
     */

    public static function getCureNum($record_id, $status = 3) {
        $num = self::find()->where(['record_id' => $record_id, 'status' => $status, 'spot_id' => self::$staticSpotId])->count();
        return $num;
    }
    
        
    /**
     * @param $dataProvider
     * @return array 获取治疗项的数量,根据reocrd_id和status分类
     */
    public static function getCureNumByList($dataProvider) {
        foreach ($dataProvider->models as $model) {
            $recordIdList[] = $model->record_id;
        }
        $query = new Query();
        $data = $query->from(['a' => self::tableName()])
                ->select(['record_id', 'status', 'count(1) as count'])
                ->where(['record_id' => $recordIdList, 'spot_id' => self::$staticSpotId])
                ->groupBy('record_id,status')
                ->all();
        $cureStatusCount = [];
        foreach ($data as $value) {
            $cureStatusCount[$value['record_id']][$value['status']] = (int)$value['count'];
        }
        return $cureStatusCount;
    }
    /*
     * 获取待治疗的项目列表
     */

    public function getCureListByRecord($record_id) {
        if (!$record_id) {
            return [];
        }
        $list = self::find()->select(['id', 'name'])->where(['spot_id' => $this->spotId,'record_id' => $record_id, 'status' => 3])->asArray()->all();
        return $list;
    }

    /**
     * @param $record_id
     * @param $status(1、已完成 2,、投资中)
     * @return 治疗情况
     */
    public function getUnderCureListByRecord($record_id, $status) {
        if (!$record_id) {
            return [];
        }
        $query = self::find();
        $query->select(['id', 'record_id', 'spot_id', 'name', 'unit', 'price', 'time', 'description', 'status', 'create_time', 'remark']);
        $query->where(['spot_id' => $this->spotId,'record_id' => $record_id, 'status' => $status]);
        $query->andWhere([ "FROM_UNIXTIME(create_time, '%Y-%m-%d')" => date("Y-m-d")]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_ASC],
                'attributes' => ['id']
            ]
        ]);
        return $dataProvider;
    }

    /**
     * @param $record_id 流水id
     * @param string $fields 查询字段
     * @return array|\yii\db\ActiveRecord[] 获取治疗的项目
     */
    public static function getCureItemList($record_id, $fields = '*') {
        $cureItemList = CureRecord::find()->select($fields)->where(['record_id' => $record_id, 'spot_id' => self::$staticSpotId])->asArray()->all();
        return $cureItemList;
    }

}
