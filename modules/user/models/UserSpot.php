<?php

namespace app\modules\user\models;

use Yii;
use yii\db\Query;
use app\modules\spot_set\models\SecondDepartment;
use app\common\base\BaseActiveRecord;
use app\modules\spot_set\models\SecondDepartmentUnion;
/**
 * This is the model class for table "{{%user_spot}}".
 *
 * @property integer $id
 * @property integer $user_id 用户id
 * @property integer $parent_spot_id 机构id
 * @property integer $spot_id 诊所id
 * @property integer $department_id 科室id
 * @property integer $status 状态(1-开放,2-不开放)
 * @property integer $create_time 创建时间
 * @property integer $update_time 更新时间
 */
class UserSpot extends BaseActiveRecord
{
    public function init(){
        parent::init();
        $this->spot_id = $this->spotId;
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_spot}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'spot_id','parent_spot_id'], 'required'],
            [['user_id', 'spot_id', 'department_id','status','create_time','update_time'], 'integer'],
            [['department_id'],'default','value'=>0],
            [['status'],'default','value' => 2]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'spot_id' => 'Spot ID',
            'department_id' => 'Department ID',
            'status' => '是否开放预约',
            'create_time' => '创建时间',
            'update_time' => '更新时间'
        ];
    }
    
    public static $getStatus = [
        1 => '是',
        2 => '否'
    ];
    /**
     * 
     * @param int $id 医生id
     * @desc 返回当前医生关联的二级科室信息
     */
    public static function getDepartmentInfo($id){
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['a.user_id','a.department_id','b.name']);
        $query->innerJoin(['b' => SecondDepartment::tableName()],'{{a}}.department_id = {{b}}.id');
        $query->leftJoin(['c' => SecondDepartmentUnion::tableName()], '{{b}}.id = {{c}}.second_department_id');
        $query->where(['a.user_id' => $id,'a.spot_id' => self::$staticSpotId,'b.status' => 1,'c.spot_id' => self::$staticSpotId]);
        return $query->all();
    }

    /**
     * @param $doctorId string 医生id
     * @return 返回当前诊所医生的开放预约状态
     */
    public static function getAppointmentStatus($doctorId){
        $data = self::find()->select('status')->where(['user_id'=>$doctorId,'spot_id'=>self::$staticSpotId])->asArray()->one();
        return $data;
    }
}
