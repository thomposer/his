<?php

namespace app\modules\spot_set\models;

use Yii;
use yii\db\Query;
use app\common\base\BaseActiveRecord;
use app\modules\spot_set\models\SecondDepartmentUnion;

/**
 * This is the model class for table "{{%second_department}}".
 *
 * @property string $id
 * @property string $name
 * @property string $parent_id
 * @property integer $status
 * @property integer $appointment_status
 * @property integer $room_type
 * @property integer $spot_id
 * @property string $create_time
 * @property string $update_time
 */
class SecondDepartment extends BaseActiveRecord
{
    public $parent_name;
    public function init() {
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%second_department}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_id', 'status', 'appointment_status', 'room_type','spot_id', 'create_time', 'update_time'], 'integer'],
            [['name', 'appointment_status','spot_id','status','parent_id'], 'required'],
            [['room_type'],'default','value' => 0],
            [['name'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => '二级科室名',
            'parent_id' => '关联一级科室',
            'parent_name' => '关联一级科室',
            'status' => '状态',
            'appointment_status' => '是否允许预约',
            'room_type' => '科室性质',           
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }
    /**
     * 
     * @var 科室状态
     */
    public static $getStatus = [
        1 => '正常',
        2 => '禁用'
    ];
    /**
     * 
     * @var 是否允许预约
     */
    public static $getAppointmentStatus = [
        1 => '是',
        2 => '否'
    ];
    /**
     * 
     * @var 科室性质
     */
    public static $getRoomType = [
        1 => '门诊',
        2 => '体检'
    ];
    /**
     * 获取二级科室的id和name列表
     */
    public static function getList($where = 'status = 1') {
        $query = new Query();
        $query->from(['a' => SecondDepartment::tableName()]);
        $query->leftJoin(['b' => SecondDepartmentUnion::tableName()], '{{a}}.id = {{b}}.second_department_id');
        $query->select(['a.id', 'a.name']);
        $query->where(['a.spot_id' => self::$staticParentSpotId, 'b.spot_id' => self::$staticSpotId]);
        $query->andWhere($where);
        $secondDepartmentInfo = $query->all();
        return $secondDepartmentInfo;
    }

    /**
     * @param integer $spotId 诊所id
     * @return 获取一二级科室列表 若诊所为null，则默认为当前诊所
     */
    public static function getOnceSecondDepartment($spotId = null)
    {
        if(is_null($spotId)){
            $spotId = self::$staticSpotId;
        }
        $query = new Query();
        $query->from(['a' => SecondDepartment::tableName()]);
        $query->select(['a.id', 'a.name', 'a.parent_id', 'b.name as onceName']);
        $query->leftJoin(['b' => OnceDepartment::tableName()], '{{a}}.parent_id = {{b}}.id');
        $query->leftJoin(['c' => SecondDepartmentUnion::tableName()], '{{a}}.id = {{c}}.second_department_id');
        $query->where(['a.status' => 1, 'a.spot_id' => self::$staticParentSpotId, 'c.spot_id' => $spotId]);
        $query->andWhere("b.name != ''");
        $query->indexBy('id');
        $departmentInfo = $query->all();
        return $departmentInfo;
    }
    /**
     * 
     * @param integer $departmentId 二级科室id
     * @return boolean 判断对应的二级科室状态是否为正常，正常返回true，否则返回false
     */
    public static function findDepartmentStatus($departmentId){
        $departmentInfo = SecondDepartment::find()->select(['id'])->where(['id' => $departmentId,'spot_id' => self::$staticParentSpotId,'status' => 1])->asArray()->one();
        if(empty($departmentInfo)){
            return true;
        }
        return false;
    }
    /**
     * 
     * @param integer $id 二级科室id
     * @param string|array $fields 查找的字段组合
     * @return \yii\db\ActiveRecord|NULL
     * @desc 返回对应的二级科室的字段信息
     */
    public static function getDepartmentFields($id,$fields = '*'){
        return self::find()->select($fields)->where(['id' => $id,'spot_id' => self::$staticParentSpotId])->asArray()->one();
    }

    /**
     * 获取二级科室的id
     */
    public static function getSecondDepartment($onceDepartment,$spotId){
        $departmentInfo = SecondDepartment::find()->select(['id'])->andFilterWhere(['parent_id' => $onceDepartment])->andFilterWhere(['spot_id' => $spotId])->asArray()->all();
        return $departmentInfo;
    }

}
