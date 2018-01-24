<?php
/**
 * @abstract 专门用来搜索查询api用的
 */
namespace app\modules\api\controllers;
use app\modules\api\controllers\CommonController;
use Yii;
use app\modules\spot_set\models\InspectClinic;
use yii\web\Response;
use app\modules\spot_set\models\CheckListClinic;
use app\modules\spot_set\models\ClinicCure;
use app\modules\spot_set\models\RecipelistClinic;
use app\modules\spot_set\models\ConsumablesClinic;
use app\modules\spot_set\models\Material;


class SearchController extends CommonController{
    
    public function behaviors(){
        
        return parent::behaviors();
    }
    
    public function actionPackageTemplateInspect(){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $name = Yii::$app->request->post('name');
        $phonetic = Yii::$app->request->post('phonetic');
        if($name === '' || $name === null){
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $where = ['or',
            ['like', 'b.inspect_name',trim($name)],
            ['like', 'b.phonetic',trim($phonetic)],
        ];
        $this->result['data'] = array_values(InspectClinic::getInspectClinicList($where));
        return $this->result;
        
    }
    public function actionPackageTemplateCheck(){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $name = Yii::$app->request->post('name');
        $meta = Yii::$app->request->post('meta');//拼音码
        if($name === '' || $name === null){
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $where = ['or',
            ['like', 'b.name',trim($name)],
            ['like', 'b.meta',trim($meta)],
        ];
        $this->result['data'] = array_values(CheckListClinic::getCheckListAll($where));
        return $this->result;
        
    }
    public function actionPackageTemplateCure(){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $name = trim(Yii::$app->request->post('name'));
        $meta = Yii::$app->request->post('meta');//拼音码
        $unit = Yii::$app->request->post('unit');//单位
        if($name === '' || $name === null){
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $where = ['or',
            ['like', 'b.name',trim($name)],
            ['like', 'b.meta',trim($meta)],
            ['like','b.unit',trim($unit)]
        ];
        $this->result['data'] = array_values(ClinicCure::getCureList(null,$where));
        return $this->result;
        
    }
    public function actionPackageTemplateRecipe(){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $name = Yii::$app->request->post('name');
        if($name === '' || $name === null){
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $where = ['OR',
            ['like', 't3.name',trim($name)],
            ['like', 't3.meta', trim($name)],
            ['like', 't3.specification', trim($name)],
            ['like', 't3.manufactor', trim($name)],
            ['like', 't3.product_name', trim($name)],
        ];
        $this->result['data'] = array_values(RecipelistClinic::getReciptListByStock($where));
        return $this->result;
        
    }
    
    public function actionClinicConsumables(){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $name = Yii::$app->request->post('name');//名称
        $meta = Yii::$app->request->post('meta');//拼音码
        $unit = Yii::$app->request->post('unit');//单位
        $specification = Yii::$app->request->post('specification');//规格
        if($name === '' || $name === null){
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $where = ['or',
            ['like', 'b.name',trim($name)],
            ['like', 'b.meta',trim($meta)],
            ['like','b.specification',trim($specification)],
            ['like','b.unit',trim($unit)]
        ];
        $this->result['data'] = array_values(ConsumablesClinic::getConsumablesList($where));
        return $this->result;
        
    }
    
    public function actionClinicMaterial(){
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        $name = Yii::$app->request->post('name');//名称
        $meta = Yii::$app->request->post('meta');//拼音码
        $unit = Yii::$app->request->post('unit');//单位
        $specification = Yii::$app->request->post('specification');//规格
        if($name === '' || $name === null){
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = '参数错误';
            return $this->result;
        }
        $where = ['and',
            ['or',
                ['like', 'name',trim($name)],
                ['like', 'meta',trim($meta)],
                ['like','specification',trim($specification)],
                ['like','unit',trim($unit)]
            ],
            ['status' => 1]
        ];
        $list = Material::getList(['id', 'name','meta','manufactor', 'price', 'tag_id', 'specification', 'unit', 'attribute'],$where);
        $this->result['data'] = array_values($list);
        return $this->result;
        
    }
}