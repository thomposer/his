<?php

namespace app\modules\nurse\models;

use app\common\base\BaseActiveRecord;
use app\modules\user\models\User;
use app\modules\user\models\UserSpot;
use Yii;
use yii\db\Query;
use app\modules\schedule\models\Scheduling;
/**
 * This is the model class for table "{{%room}}".
 *

 * @property integer $user_id
 * @property integer $doctor_id
 */
class NurseDoctorConfig extends BaseActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName(){
        return '{{%nurse_doctor_config}}';
    }


    public function rules()
    {
        return [
            [['user_id','spot_id','create_time', 'update_time'], 'integer'],
            [['doctor_id'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => '护士id',
            'doctor_id' => '医生id',
            'spot_id' => '诊所id',
            'create_time' => '创建时间',
            'update_time' => '更新时间',
        ];
    }

    /**
     * @param $userId 护士id
     * @return array 护士关注的医生
     */
    public static function getDoctorList($userId){
        $query = new Query();
        $query->from(['a' => self::tableName()]);
        $query->select(['DISTINCT(a.id)','a.user_id','a.doctor_id','a.spot_id','doctorName'=>'b.username']);
        $query->leftJoin(['b' => User::tableName()], '{{a}}.doctor_id = {{b}}.id');
        $query->leftJoin(['c' => UserSpot::tableName()], '{{b}}.id = {{c}}.user_id');
        $query->where(['c.spot_id' => self::$staticSpotId, 'b.status' => 1,'a.user_id' => $userId,'b.occupation'=>2]);
        $query->orderBy(['b.create_time'=>SORT_ASC]);
        $query->indexBy('doctor_id');
        $all = $query->all();
        return $all;
    }

    /**
     * @param $date
     * @return array 当前诊所的排班医生
     */
    public static function getschedulingDoctor($date){
        $query = new Query();
        $query->from([ 'a'=>Scheduling::tableName()]);
        $query->select(['DISTINCT(a.user_id)']);
        $query->leftJoin(['b' => UserSpot::tableName()], '{{a}}.user_id = {{b}}.user_id');
        $query->where(['a.spot_id' => self::$staticSpotId]);
        $query->andWhere(['between', 'schedule_time', strtotime($date), strtotime($date) + 86400 - 1]);
        $all = $query->all();
        return $all;
    }

}
