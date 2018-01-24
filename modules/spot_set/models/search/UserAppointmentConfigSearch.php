<?php

namespace app\modules\spot_set\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\UserAppointmentConfig;
use yii\db\ActiveQuery;
use app\modules\user\models\User;
use app\modules\spot_set\models\SecondDepartment;
use app\modules\user\models\UserSpot;
use app\modules\spot_set\models\SpotType;

/**
 * UserAppointmentConfigSearch represents the model behind the search form about `app\modules\spot_set\models\UserAppointmentConfig`.
 */
class UserAppointmentConfigSearch extends UserAppointmentConfig
{
    public $userName;//医生名称
    public $departmentName;//二级科室名称
    public $status;//是否开放预约
    public $appointmentTypeName;//预约类型服务名称
    public $appointmentTypeId;//预约类型服务对应spot_id
    public $appointmentId;//预约类型服务对应id
    public $appointmentStatus;//预约类型服务对应状态
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userName','departmentName','appointmentTypeName'],'string'],
            [['id', 'spot_id', 'user_id', 'spot_type_id', 'create_time', 'update_time','status'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userName' => '医生',
            'departmentName' => '科室',
            'status' => '是否开放预约',
            'appointmentTypeName' => '可提供服务',
        ];
    }
    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params,$pageSize = 20)
    {
//         $query = UserAppointmentConfig::find();
        $query = new ActiveQuery(self::className());
//         $query->from(['a' => User::tableName()]);
//         $query->select(['id' => 'a.user_id','userName'=>'b.username','c.status','departmentName' => 'group_concat(distinct d.name)','appointmentTypeName'=>"group_concat(distinct e.type SEPARATOR ' | ')"]);
//         $query->leftJoin(['b' => User::tableName()],'{{a}}.user_id = {{b}}.id');
//         $query->leftJoin(['c' => UserSpot::tableName()],'{{b}}.id = {{c}}.user_id');
//         $query->leftJoin(['d' => SecondDepartment::tableName()],'{{c}}.department_id = {{d}}.id');
//         $query->leftJoin(['e' => SpotType::tableName()],'{{a}}.spot_type_id = {{e}}.id');
        $query->from(['a' => User::tableName()]);
        $query->select(['a.id','userName' => 'a.username','b.status','departmentName' => 'group_concat(distinct c.name)']);
        $query->leftJoin(['b' => UserSpot::tableName()],'{{a}}.id = {{b}}.user_id');
        $query->leftJoin(['c' => SecondDepartment::tableName()],'{{b}}.department_id = {{c}}.id');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'b.spot_id' => $this->spot_id,
        ]);
        $query->andFilterWhere(['a.occupation' => 2]);
        $query->groupBy('a.id');
        return $dataProvider;
    }
}
