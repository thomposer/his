<?php

namespace app\modules\module\models\search;

use Yii;
use app\modules\module\models\Title;
use yii\data\ActiveDataProvider;

class TitleSearch extends Title {
	
	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['module_description'], 'safe'],
		];
	}
	
	/**
	 * @inheritdoc
	 */
	public function scenarios()
	{
		// bypass scenarios() implementation in the parent class
		return Title::scenarios();
	}
	
	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 *
	 * @return ActiveDataProvider
	 */
	public function search($params, $pageSize = 10,$where = NULL)
	{
		$query = Title::find();		
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        	'pagination' => [
				'pageSize' => $pageSize
        	],
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_DESC
                ]
            ]
        ]);
        
        $this->load($params);
		
		if (!$this->validate()) {
		   
			return $dataProvider;
		}
	
		$query->andFilterWhere(['like', 'module_description', $this->module_description]);
		$query->andWhere($where);
		
/* 		
		$user = Yii::$app->user->identity->userInfo;
		
		if (!$user->isSuperUser()) {
			$query->andWhere(['like', 'title', $this->title]);
		} */
		
		return $dataProvider;
	}
}

?>