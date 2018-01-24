<?php

namespace app\modules\spot_set\controllers;

use app\common\base\BaseController;
use app\modules\spot_set\models\OutpatientPackageTemplate;
use app\modules\spot_set\models\search\OutpatientPackageTemplateSearch;
use Yii;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use app\common\base\MultiModel;
use app\modules\spot_set\models\OutpatientPackageRecipe;
use app\modules\spot_set\models\OutpatientPackageInspect;
use app\modules\spot_set\models\OutpatientPackageCheck;
use app\modules\spot_set\models\OutpatientPackageCure;
use app\modules\spot_set\models\MedicalFeeClinic;
use app\modules\spot_set\models\InspectClinic;
use app\modules\spot_set\models\CheckListClinic;
use app\modules\spot_set\models\ClinicCure;
use app\modules\spot_set\models\RecipelistClinic;
use yii\data\ActiveDataProvider;
use yii\db\Exception;
use yii\db\ActiveQuery;
use app\modules\spot\models\CureList;
use yii\db\Query;
use app\modules\spot\models\RecipeList;

class OutpatientPackageTemplateController extends BaseController{

    public function actionPackageTemplateIndex(){

        $searchModel=new OutpatientPackageTemplateSearch();
        $dataProvider=$searchModel->search(Yii::$app->request->queryParams,$this->pageSize);
        return $this->render('package-template-index',[
            'searchModel'=>$searchModel,
            'dataProvider'=>$dataProvider

        ]);

    }
    //新增医嘱模板/套餐
    public function actionPackageTemplateCreate(){
        
        $model = new MultiModel([
            'models' => [
                'packageTemplate' => new OutpatientPackageTemplate(),
                'packageInspect' => new OutpatientPackageInspect(),
                'packageCheck' => new OutpatientPackageCheck(),
                'packageCure' => new OutpatientPackageCure(),
                'packageRecipe' => new OutpatientPackageRecipe(),
            ]
        ]);
        $model->getModel('packageCure')->scenario = 'create';
        $model->getModel('packageTemplate')->type = 1;
        $request = Yii::$app->request;
        if(!$request->isGet){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($model->load(Yii::$app->request->post()) && $model->validate()){
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $model->getModel('packageTemplate')->save();
                    $packageInspectModel = $model->getModel('packageInspect');
                    $packageCheckModel = $model->getModel('packageCheck');
                    $packageCureModel = $model->getModel('packageCure');
                    $packageRecipeModel = $model->getModel('packageRecipe');
                    OutpatientPackageInspect::saveInfo($model->getModel('packageTemplate')->id, $packageInspectModel,1);
                    OutpatientPackageCheck::saveInfo($model->getModel('packageTemplate')->id,$packageCheckModel,1);
                    OutpatientPackageCure::saveInfo($model->getModel('packageTemplate')->id,$packageCureModel,1);
                    OutpatientPackageRecipe::saveInfo($model->getModel('packageTemplate')->id, $packageRecipeModel,1);
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
                    
                }catch (Exception $e){
                    $dbTrans->rollBack();
                    
                    Yii::error($e->errorInfo,'outpatient-package-template-create');
                }
                
                
            }else{
                 $this->result['errorCode'] = 1001;
                 if($model->errors['packageTemplate']){
                     $error = $model->errors['packageTemplate'][0][0];
                 }else if($model->errors['packageCure']){
                     $error = $model->errors['packageCure'][0][0];
                 }else {
                     $error = $model->errors['packageRecipe'][0][0];
                 }
                 $this->result['msg'] = $error;
                 return $this->result;
            }
        }else{
            $cureDataProvider = new ActiveDataProvider([
                'query' => OutpatientPackageCure::find()->select(['id'])->where(['id' => 0]),
                'pagination' => false,
                'sort' => [
                    'attributes' => ['']
                ]
            ]);
            return $this->render('create',[
                'model' => $model,
                'cureDataProvider' => $cureDataProvider
            ]);
        }
        
        
    }
    /**
     * 
     * @param 模板id $id
     * @return string 编辑医嘱模板套餐信息
     */
    public function actionPackageTemplateUpdate($id){
        $model = new MultiModel([
            'models' => [
                'packageTemplate' => $this->findModel($id),
                'packageInspect' => new OutpatientPackageInspect(),
                'packageCheck' => new OutpatientPackageCheck(),
                'packageCure' => new OutpatientPackageCure(),
                'packageRecipe' => new OutpatientPackageRecipe(),
            ]
        ]);
        $model->getModel('packageCure')->scenario = 'create';
        $request = Yii::$app->request;
        if(!$request->isGet){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($model->load(Yii::$app->request->post()) && $model->validate()){
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $model->getModel('packageTemplate')->save();
                    $packageInspectModel = $model->getModel('packageInspect');
                    $packageCheckModel = $model->getModel('packageCheck');
                    $packageCureModel = $model->getModel('packageCure');
                    $packageRecipeModel = $model->getModel('packageRecipe');
                    OutpatientPackageInspect::saveInfo($model->getModel('packageTemplate')->id, $packageInspectModel,2);
                    OutpatientPackageCheck::saveInfo($model->getModel('packageTemplate')->id,$packageCheckModel,2);
                    OutpatientPackageCure::saveInfo($model->getModel('packageTemplate')->id,$packageCureModel,2);
                    OutpatientPackageRecipe::saveInfo($model->getModel('packageTemplate')->id, $packageRecipeModel,2);
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
        
                }catch (Exception $e){
                    $dbTrans->rollBack();
        
                    Yii::error($e->errorInfo,'outpatient-package-template-create');
                }
        
            }else{
                $this->result['errorCode'] = 1001;
                if($model->errors['packageTemplate']){
                    $error = $model->errors['packageTemplate'][0][0];
                }else if($model->errors['packageCure']){
                    $error = $model->errors['packageCure'][0][0];
                }else {
                    $error = $model->errors['packageRecipe'][0][0];
                }
                $this->result['msg'] = $error;
                return $this->result;
            }
        }else{
            $cureQuery = new ActiveQuery(OutpatientPackageCure::className());
            $cureQuery->from(['a' => OutpatientPackageCure::tableName()]);
            $cureQuery->select(['a.id','a.cure_id','a.time','a.description','c.name','c.unit']);
            $cureQuery->leftJoin(['b' => ClinicCure::tableName()],'{{a}}.cure_id = {{b}}.id');
            $cureQuery->leftJoin(['c' => CureList::tableName()],'{{b}}.cure_id = {{c}}.id');
            $cureQuery->where(['a.outpatient_package_id' => $id,'a.spot_id' => $this->spotId,'b.status' => 1]);
            $cureDataProvider = new ActiveDataProvider([
                'query' => $cureQuery,
                'pagination' => false,
                'sort' => [
                    'attributes' => ['']
                ]
            ]);
            $recipeDataProvider = $this->findRecipeDataProvider($id);
            $inspectDataProvider = OutpatientPackageInspect::getInspectList($id);
            $checkDataProvider = OutpatientPackageCheck::getCheckList($id);
            $disabledCure = array_column($recipeDataProvider, 'outpatient_package_cure_id');
            return $this->render('update',[
                'model' => $model,
                'inspectDataProvider' => $inspectDataProvider,
                'checkDataProvider' => $checkDataProvider,
                'cureDataProvider' => $cureDataProvider,
                'recipeDataProvider' => $recipeDataProvider,
                'disabledCure' => $disabledCure
            ]);
        }
    }
    
    public function actionPackageTemplateDelete($id){
        $request=Yii::$app->request;
            if($request->isAjax){
                $this->findModel($id)->delete();
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
            }else{
                return $this->redirect(['package-recipe-type']);
            }
    }
    
    public function findModel($id){
        if(($model=OutpatientPackageTemplate::findOne(['id'=>$id,'spot_id'=>$this->spotId]))!==null){
            return $model;
        }else{
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
    
    protected function findRecipeDataProvider($id){
        $query = new Query();
        $query->from(['a' => OutpatientPackageRecipe::tableName()]);
        $query->select(['a.id', 'a.clinic_recipe_id', 'a.dose', 'a.dose_unit', 'a.used', 'a.frequency', 'a.day', 'a.num',
            'a.description', 'a.type', 'a.skin_test_status','a.outpatient_package_cure_id', 'c.skin_test', 'a.curelist_id', 'c.dose_unit as recipe_dose_unit',
            'c.specification','c.manufactor', 'd.price', 'c.name', 'c.medicine_description_id', 'c.type as recipeType','c.unit'
        ]);
        $query->leftJoin(['d' => RecipelistClinic::tableName()], '{{a}}.clinic_recipe_id = {{d}}.id');
        $query->leftJoin(['c' => RecipeList::tableName()], '{{d}}.recipelist_id = {{c}}.id');
        $query->where(['a.outpatient_package_id' => $id,'a.spot_id' => $this->spotId,'c.status' =>1]);
        $query->orderBy(['a.id' => SORT_ASC]);
        $result = $query->all();
        foreach ($result as $key => $value) {
            $doseUnit = explode(',', $value['recipe_dose_unit']);
            foreach ($doseUnit as $vals) {
                $all_dose_unit[$vals] = RecipeList::$getDoseUnit[$vals];
            }
            $result[$key]['recipe_dose_unit'] = $all_dose_unit;
        }
        return $result;
    
    }
    
  
}