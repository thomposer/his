<?php

namespace app\modules\spot\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\CheckCode;


/**
 * checkCodeSearch represents the model behind the search form about `app\modules\check_code\models\checkCode`.
 */
class CheckCodeSearch extends CheckCode
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['name', 'major_code', 'add_code', 'help_code', 'remark'], 'safe'],
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
        $query = checkCode::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['id' => SORT_ASC],
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
            'spot_id' => $this->spot_id,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'major_code', $this->major_code])
            ->andFilterWhere(['like', 'add_code', $this->add_code])
            ->andFilterWhere(['like', 'help_code', $this->help_code])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
