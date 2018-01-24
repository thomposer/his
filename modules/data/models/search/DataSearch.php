<?php

namespace app\modules\data\models\search;

use yii\base\Model;

/**
 * ReportFormsSearch represents the model behind the search form about `app\modules\report_forms\models\ReportForms`.
 */
class DataSearch extends \yii\db\ActiveRecord
{

    public $beginTime;
    public $endTime;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['beginTime','endTime'],'date'],
            [['beginTime','endTime'],'required'],
            ['endTime','compare', 'operator'=>'>=', 'compareAttribute'=>'beginTime']
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
}
