<?php

namespace app\modules\manage\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\awpmanage\models\Service;

/**
 * ServiceSearch represents the model behind the search form about `app\modules\awpmanage\models\Service`.
 */
class ServiceSearch extends Service
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['wxname', 'wxcode', 'maininfo', 'appid', 'appsecret', 'url', 'token', 'aeskey', 'remark'], 'safe'],
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
    public function search($params)
    {
        $query = Service::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'wxname', $this->wxname])
            ->andFilterWhere(['like', 'wxcode', $this->wxcode])
            ->andFilterWhere(['like', 'maininfo', $this->maininfo])
            ->andFilterWhere(['like', 'appid', $this->appid])
            ->andFilterWhere(['like', 'appsecret', $this->appsecret])
            ->andFilterWhere(['like', 'url', $this->url])
            ->andFilterWhere(['like', 'token', $this->token])
            ->andFilterWhere(['like', 'aeskey', $this->aeskey])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
