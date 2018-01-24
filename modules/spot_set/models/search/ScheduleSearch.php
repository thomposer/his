<?php

namespace app\modules\spot_set\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\Schedule;

/**
 * ScheduleSearch represents the model behind the search form about `app\modules\spot_set\models\Schedule`.
 */
class ScheduleSearch extends Schedule {

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['id', 'status', 'create_time', 'update_time'], 'integer'],
            [['shift_name', 'shift_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios() {
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
    public function search($params, $pageSize = 20) {
        $query = Schedule::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
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
            'status' => $this->status,
            'spot_id' => $this->spot_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'shift_name', trim($this->shift_name)])
                ->andFilterWhere(['like', 'shift_time', $this->shift_time]);

        return $dataProvider;
    }

}
