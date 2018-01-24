<?php

namespace app\modules\doctor\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\doctor\models\Doctor;

/**
 * DoctorSearch represents the model behind the search form about `app\modules\doctor\models\Doctor`.
 */
class DoctorSearch extends Doctor
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'record_id', 'spot_id', 'incidence_date', 'heightcm', 'temperature_type', 'temperature', 'breathing', 'pulse', 'shrinkpressure', 'diastolic_pressure', 'oxygen_saturation', 'pain_score', 'doctor_id', 'room_id', 'create_time', 'update_time', 'diagnosis_time', 'triage_time'], 'integer'],
            [['weightkg'], 'number'],
            [['bloodtype', 'personalhistory', 'genetichistory', 'allergy', 'chiefcomplaint', 'historypresent', 'case_reg_img', 'pasthistory', 'physical_examination', 'examination_check', 'first_check', 'cure_idea', 'remark'], 'safe'],
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
        $query = Doctor::find();

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
            'record_id' => $this->record_id,
            'spot_id' => $this->spot_id,
            'incidence_date' => $this->incidence_date,
            'heightcm' => $this->heightcm,
            'weightkg' => $this->weightkg,
            'temperature_type' => $this->temperature_type,
            'temperature' => $this->temperature,
            'breathing' => $this->breathing,
            'pulse' => $this->pulse,
            'shrinkpressure' => $this->shrinkpressure,
            'diastolic_pressure' => $this->diastolic_pressure,
            'oxygen_saturation' => $this->oxygen_saturation,
            'pain_score' => $this->pain_score,
            'doctor_id' => $this->doctor_id,
            'room_id' => $this->room_id,
            'create_time' => $this->create_time,
            'update_time' => $this->update_time,
            'diagnosis_time' => $this->diagnosis_time,
            'triage_time' => $this->triage_time,
        ]);

        $query->andFilterWhere(['like', 'bloodtype', $this->bloodtype])
            ->andFilterWhere(['like', 'personalhistory', $this->personalhistory])
            ->andFilterWhere(['like', 'genetichistory', $this->genetichistory])
            ->andFilterWhere(['like', 'allergy', $this->allergy])
            ->andFilterWhere(['like', 'chiefcomplaint', $this->chiefcomplaint])
            ->andFilterWhere(['like', 'historypresent', $this->historypresent])
            ->andFilterWhere(['like', 'case_reg_img', $this->case_reg_img])
            ->andFilterWhere(['like', 'pasthistory', $this->pasthistory])
            ->andFilterWhere(['like', 'physical_examination', $this->physical_examination])
            ->andFilterWhere(['like', 'examination_check', $this->examination_check])
            ->andFilterWhere(['like', 'first_check', $this->first_check])
            ->andFilterWhere(['like', 'cure_idea', $this->cure_idea])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
