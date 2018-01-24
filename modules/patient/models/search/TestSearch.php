<?php

namespace app\modules\patient\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\patient\models\Patient;

/**
 * TestSearch represents the model behind the search form about `app\modules\patient\models\Patient`.
 */
class TestSearch extends Patient
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'sex', 'birthday', 'marriage', 'create_time', 'update_time'], 'integer'],
            [['username', 'iphone', 'email', 'head_img', 'card', 'nation', 'occupation', 'worker', 'remark', 'province', 'city', 'area', 'detail_address'], 'safe'],
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
        $query = Patient::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'spot_id' => $this->spot_id,
            'sex' => $this->sex,
            'birthday' => $this->birthday,
            'marriage' => $this->marriage,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['like', 'iphone', $this->iphone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'head_img', $this->head_img])
            ->andFilterWhere(['like', 'card', $this->card])
            ->andFilterWhere(['like', 'nation', $this->nation])
            ->andFilterWhere(['like', 'occupation', $this->occupation])
            ->andFilterWhere(['like', 'worker', $this->worker])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['like', 'province', $this->province])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'area', $this->area])
            ->andFilterWhere(['like', 'detail_address', $this->detail_address]);

        return $dataProvider;
    }
}
