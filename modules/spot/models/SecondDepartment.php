<?php

namespace app\modules\spot\models;

use app\modules\spot_set\models\SecondDepartmentUnion;
use Yii;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\OnceDepartment;
use yii\db\Query;

/**
 * This is the model class for table "{{%second_department}}".
 *
 * @property string $id
 * @property string $name
 * @property string $parent_id
 * @property integer $status
 * @property integer $appointment_status
 * @property integer $room_type
 * @property string $spot_id
 * @property string $create_time
 * @property string $update_time
 */
class SecondDepartment extends \app\common\base\BaseActiveRecord
{
    public $parent_name;
    public $onceDepartmentId;
    public function init() {
        parent::init();
        $this->spot_id = $this->parentSpotId;
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
            [['name','spot_id','status','parent_id'], 'required'],
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
     * @var 科室性质
     */
    public static $getRoomType = [
        1 => '门诊',
        2 => '体检'
    ];

    /**
     * @param $id 一级科室id
     * @return 获取二级科室
     */
    public static function findSubDataProvider($id) {
        $query = new \yii\db\ActiveQuery(self::className());
        $query->from(['a' => SecondDepartment::tableName()]);
        $query->select(['a.id','a.name', 'a.status', 'a.room_type','onceDepartmentId' => 'c.id']);
        $query->leftJoin(['b' => SecondDepartmentUnion::tableName()], '{{a}}.id = {{b}}.second_department_id');
        $query->leftJoin(['c' => OnceDepartment::tableName()], '{{a}}.parent_id = {{c}}.id');
        $query->where(['parent_id' => $id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
            'sort' => [
                'attributes' => ['']
            ]
        ]);
        return $dataProvider;
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
        $query->where(['a.status' => 1, 'a.spot_id' => self::$staticSpotId]);
        $query->andWhere("b.name != ''");
        $query->indexBy('id');
        $departmentInfo = $query->all();
        return $departmentInfo;
    }
}
