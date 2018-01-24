<?php

namespace app\modules\message\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\message\models\MessageCenter;

/**
 * MessageCenterSearch represents the model behind the search form about `app\modules\message\models\MessageCenter`.
 */
class MessageCenterSearch extends MessageCenter
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'patient_id', 'status', 'create_time', 'update_time'], 'integer'],
            [['type', 'content'], 'safe'],
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
        $query = MessageCenter::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
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
            'patient_id' => $this->patient_id,
            'status' => $this->status,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'type', $this->type])
            ->andFilterWhere(['like', 'content', $this->content]);

        return $dataProvider;
    }
}
