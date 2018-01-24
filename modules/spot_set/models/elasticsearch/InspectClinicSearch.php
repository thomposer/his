<?php

namespace app\modules\spot_set\models\elasticsearch;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\InspectClinic;
use yii\elasticsearch\ActiveRecord;

/**
 * InspectClinicSearch represents the model behind the search form about `app\modules\spot_set\models\InspectClinic`.
 */
class InspectClinicSearch extends ActiveRecord
{

     public function attributes(){
         
         return ['title','desc'];
     }
    
     public static function index(){
         return 'his_test';
     }
     
     public static function type(){
         
         return 'products';
     }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 20) {
        
    }

}
