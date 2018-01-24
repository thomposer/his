<?php

namespace app\modules\spot_set\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\Room;
use app\modules\spot_set\models\DoctorRoomUnion;
use app\modules\user\models\User;
use app\modules\user\models\UserSpot;

/**
 * RoomSearch represents the model behind the search form about `app\modules\spot_set\models\Room`.
 */
class RoomSearch extends Room
{
    public $doctorId;//医生ID
    public $doctorName;//医生名称
    public $unionId;//关联诊室ID
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'floor', 'clinic_type', 'status', 'spot_id', 'create_time', 'update_time'], 'integer'],
            [['clinic_name'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params,$pageSize = 20)
    {
        $query = Room::find()->select(['id','clinic_name','floor','clinic_type','status'])->where('status != 3');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_DESC
                ],
                'attributes' => ['id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'floor' => $this->floor,
            'clinic_type' => $this->clinic_type,
            'spot_id' => $this->spot_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);
        $query->andFilterWhere(['like', 'clinic_name', trim($this->clinic_name)]);

        return $dataProvider;
    }
    
    
     public function doctorRoomSearch($params,$pageSize = 20)
    {
        $query = new \yii\db\ActiveQuery(self::className());
        $query->from(['a' => UserSpot::tableName()]);
        $query->select(['doctorId' => 'b.id', 'doctorName' => 'b.username', 'unionId' => 'group_concat(distinct c.room_id)']);
        $query->leftJoin(['b' => User::tableName()], '{{a}}.user_id = {{b}}.id');
        $query->leftJoin(['c' => DoctorRoomUnion::tableName()], '{{b}}.id = {{c}}.doctor_id');
        

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => false,
            ],
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }
        
        $query->where(['a.spot_id' => $this->spot_id, 'b.status' => 1,'b.occupation' => 2]);
        $query->groupBy('b.id');
        
        return $dataProvider;
    }
   
}
