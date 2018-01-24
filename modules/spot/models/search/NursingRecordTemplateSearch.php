<?php

namespace app\modules\spot\models\search;

use app\modules\user\models\User;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot\models\NursingRecordTemplate;
use yii\db\ActiveQuery;

/**
 * NursingRecordTemplateSearch represents the model behind the search form about `app\modules\spot\models\NursingRecordTemplate`.
 */
class NursingRecordTemplateSearch extends NursingRecordTemplate
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'spot_id', 'operating_id', 'create_time', 'update_time'], 'integer'],
            [['nursing_item', 'content_template'], 'safe'],
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
        $query = new ActiveQuery(NursingRecordTemplate::className());
        $query->select(['nrt.id','nrt.nursing_item','nrt.create_time','u.username']);
        $query->from(['nrt' => NursingRecordTemplate::tableName()]);
        $query->leftJoin(['u' => User::tableName()], '{{nrt}}.operating_id={{u}}.id');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => $pageSize,
            ],
            'sort' => [
                'defaultOrder' => ['nrt.id' => SORT_DESC],
                'attributes' => ['nrt.id']
            ]
        ]);

        $this->load($params);
        if (!$this->validate()) {
            $query->where('1=0');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'nrt.id' => $this->id,
            'nrt.spot_id' => $this->spot_id,
            'nrt.operating_id' => $this->operating_id,
            'nrt.create_time' => $this->create_time,
            'nrt.update_time' => $this->update_time,
        ]);

        $query->andFilterWhere(['like', 'nrt.nursing_item', $this->nursing_item])
            ->andFilterWhere(['like', 'nrt.content_template', $this->content_template]);

        return $dataProvider;
    }
}
