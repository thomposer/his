<?php

namespace app\modules\user\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\user\models\User;

/**
 * UserSearch represents the model behind the search form about `app\modules\user\models\User`.
 */
class UserSearch extends User
{
	
	public $spot;
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'type', 'status', 'created_at', 'updated_at'], 'integer'],
            [['user_id', 'username'], 'safe'],
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
    	$parentLabels = parent::attributeLabels();
    	$parentLabels['spot'] = '站点';
    	return $parentLabels;
    }
    
    public function load($data, $formName = null) {
    	if (isset($data[$this->formName()]['spot'])) {
    		$this->spot = $data[$this->formName()]['spot'];
    	}
    	parent::load($data, $formName);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params,$pageSize = 10)
    {
        $query = User::find()->orderBy(['updated_at' => SORT_DESC]);

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
            'type' => $this->type,
            
        ]);

        $query->andFilterWhere(['like', 'user_id', $this->user_id])
            ->andFilterWhere(['like', 'username', $this->username]);

        return $dataProvider;
    }
}
