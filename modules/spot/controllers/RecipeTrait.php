<?php

namespace app\modules\spot\controllers;

use Yii;
use app\modules\spot\models\RecipeList;
use app\modules\spot\models\search\RecipeListSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use app\modules\medicine\models\MedicineDescription;
use app\modules\spot_set\models\RecipelistClinic;
use app\modules\spot\models\Tag;
use yii\db\Exception;
use app\modules\spot\models\AdviceTagRelation;
use app\modules\spot\models\Spot;
use app\modules\spot\models\ConfigureClinicUnion;
use app\modules\charge\models\ChargeInfo;

/**
 * RecipeListController implements the CRUD actions for RecipeList model.
 */
trait RecipeTrait
{
    /**
     * Lists all RecipeList models.
     * @return mixed
     */
    public function actionRecipeIndex()
    {
        $searchModel = new RecipeListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);
        $spotNameList = ConfigureClinicUnion::getClinicNameListString($dataProvider->keys, ChargeInfo::$recipeType);
        $spotList = Spot::getSpotList(['status' => 1]);
        return $this->render('recipe/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'spotNameList' => $spotNameList,
            'spotList' => $spotList,
        ]);
    }

    /**
     * Displays a single RecipeList model.
     * @param string $id
     * @return mixed
     */
    public function actionRecipeView($id)
    {
        $model=$this->findRecipeModel($id);

        $model->dose_unit=explode(',',$model->dose_unit);

        foreach($model->dose_unit as $key => $val){
            $does_unit[]=RecipeList::$getDoseUnit[$val];
        }
        $model->dose_unit=implode(',',$does_unit);

        return $this->render('recipe/view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new RecipeList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionRecipeCreate()
    {
        
        $model = new RecipeList();
        $model->scenario = 'update';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $model->save();
                if (!ConfigureClinicUnion::saveInfo($model->id, $model->unionSpotId, ChargeInfo::$recipeType)) {
                    $dbTrans->rollBack();
                    Yii::error('保存处方配置适用诊所失败', 'spot-recipelist-create');
                    Yii::$app->getSession()->setFlash('error','保存失败');
                    return $this->redirect(['recipe-index']);
                }
                if(!empty($model->adviceTagId) && is_array($model->adviceTagId)){
                    $rows = [];
                    foreach ($model->adviceTagId as $v){
                        $rows [] = [$this->parentSpotId,$model->id,$v,AdviceTagRelation::$recipeType,time(),time()];
                    }
                    Yii::$app->db->createCommand()->batchInsert(AdviceTagRelation::tableName(),['spot_id','advice_id','tag_id','type','create_time','update_time'], $rows)->execute();
                }
                $dbTrans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(['recipe-index']);
                
            }catch (Exception $e){
                $dbTrans->rollBack();
                Yii::error($e->errorInfo,'spot-recipelist-create');
            }
            
        } else {
            $medicineDescription = MedicineDescription::getList();
            $commonTagList = Tag::getTagList(['id','name'],['type' => 2]);
            $spotList = Spot::getSpotList(['status' => 1]);
            return $this->render('recipe/create', [
                'model' => $model,
                'medicineDescription' => $medicineDescription,
                'commonTagList' => $commonTagList,
                'spotList' => $spotList,
            ]);
        }
    }

    /**
     * Updates an existing RecipeList model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionRecipeUpdate($id)
    {
        $model = $this->findRecipeModel($id);
        $model->scenario = 'update';
        $tagList = AdviceTagRelation::getTagList(['tag_id'],['advice_id' => $id,'type' => AdviceTagRelation::$recipeType]);

        if(!empty($tagList)){
            $model->adviceTagId = array_column($tagList, 'tag_id');
        }
        
        $spotIdList = ConfigureClinicUnion::getClinicIdList(['configure_id' => $id,'type' => ChargeInfo::$recipeType]);
        if(!empty($spotIdList)){
            $model->unionSpotId = array_column($spotIdList, 'spot_id');
        }
        
        $model->dose_unit=explode(',',$model->dose_unit);
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $model->save();
                if (!ConfigureClinicUnion::saveInfo($model->id, $model->unionSpotId, ChargeInfo::$recipeType)) {
                    $dbTrans->rollBack();
                    Yii::error('保存处方配置适用诊所失败', 'spot-recipelist-update');
                    Yii::$app->getSession()->setFlash('error','保存失败');
                    return $this->redirect(['recipe-index']);
                }
                AdviceTagRelation::deleteAll(['spot_id' => $this->parentSpotId,'advice_id' => $id,'type' => AdviceTagRelation::$recipeType]);
                if(!empty($model->adviceTagId) && is_array($model->adviceTagId)){
                    $rows = [];
                    foreach ($model->adviceTagId as $v){
                        $rows [] = [$this->parentSpotId,$model->id,$v,AdviceTagRelation::$recipeType,time(),time()];
                    }
                    Yii::$app->db->createCommand()->batchInsert(AdviceTagRelation::tableName(),['spot_id','advice_id','tag_id','type','create_time','update_time'], $rows)->execute();
                }
                $dbTrans->commit();
                Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(['recipe-index']);
                
            }catch (Exception $e){
                $dbTrans->rollBack();
                Yii::error($e->errorInfo,'spot-recipelist-update');
            }
        } else {
            $medicineDescription = MedicineDescription::getList();
            $commonTagList = Tag::getTagList(['id','name'],['type' => 2]);
            $spotList = Spot::getSpotList(['status' => 1]);
            return $this->render('recipe/update', [
                'model' => $model,
                'medicineDescription' => $medicineDescription,
                'commonTagList' => $commonTagList,
                'spotList' => $spotList,
            ]);
        }
    }

    /**
     * Deletes an existing RecipeList model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionRecipeDelete($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
        
            /*
             *   Process for ajax request
             */
            $model = $this->findRecipeModel($id);
            $model->status = $model->status == 2 ? 1 : 2;
            $model->scenario = 'updateStatus';
            $model->save();
            Yii::error($model->errors, 'recipeList');
            RecipelistClinic::updateAll(['status' => $model->status],['recipelist_id' => $id]);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['recipe-index']);
        }
    }
    /**
     * Finds the RecipeList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return RecipeList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findRecipeModel($id)
    {
        if (($model = RecipeList::findOne(['id' => $id,'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在.');
        }
    }


}
