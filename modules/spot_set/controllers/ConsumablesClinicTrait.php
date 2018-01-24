<?php

namespace app\modules\spot_set\controllers;

use Yii;
use app\modules\spot_set\models\ConsumablesClinic;
use app\modules\spot_set\models\search\ConsumablesClinicSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use app\modules\spot\models\Consumables;
use app\modules\spot\models\Tag;
use yii\db\ActiveQuery;

/**
 * ConsumablesClinicController implements the CRUD actions for ConsumablesClinic model.
 */
trait ConsumablesClinicTrait
{

    /**
     * Lists all ConsumablesClinic models.
     * @return mixed
     */
    public function actionConsumablesClinicIndex()
    {    
        $searchModel = new ConsumablesClinicSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);

        return $this->render('consumables-clinic/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new ConsumablesClinic model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionConsumablesClinicCreate()
    {
        $request = Yii::$app->request;
        $model = new ConsumablesClinic();  

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'forceClose' => true,
                    'forceMessage' => '操作成功'
                ];         
            }else{      
                $fields = ['a.name','a.product_name','a.en_name','a.type','a.specification','a.unit','a.meta','a.manufactor','a.remark'];
                $consumablesList = Consumables::getList($fields,['a.status' => 1],true);
                return [
                    'title'=> "新增医疗耗材",
                    'content'=>$this->renderAjax('consumables-clinic/create', [
                        'model' => $model,
                        'consumablesList' => $consumablesList
                    ]),
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                               Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
        
                ];         
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
       
    }

    /**
     * Updates an existing ConsumablesClinic model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionConsumablesClinicUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findConsumablesActiveQueryModel($id);

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'forceClose' => true,
                    'forceMessage' => '操作成功'
                ];    
            }else{
                 return [
                    'title'=> "编辑医疗耗材",
                    'content'=>$this->renderAjax('consumables-clinic/update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                               Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
                ];        
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
    /**
     * Displays a single ClinicCure model.
     * @param integer $id
     * @return mixed
     */
    public function actionConsumablesClinicView($id)
    {
        $model = $this->findConsumablesActiveQueryModel($id);
    
        return $this->render('consumables-clinic/view', [
            'model' => $model
        ]);
    }
    /**
     * Delete an existing ConsumablesClinic model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionConsumablesClinicDelete($id)
    {

        $request = Yii::$app->request;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            $this->findConsumablesModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['consumables-clinic-index']);
        }      

    }

    /**
     * Finds the ConsumablesClinic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ConsumablesClinic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findConsumablesModel($id)
    {
        if (($model = ConsumablesClinic::findOne(['id' => $id,'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
    
    
    protected function findConsumablesActiveQueryModel($id){
        $query = new ActiveQuery(ConsumablesClinic::className());
        $query->from(['a' => ConsumablesClinic::tableName()]);
        $query->select(['a.*','b.name','b.product_name','b.en_name','b.type','b.specification','b.unit','b.meta','b.manufactor','b.remark','tag_name' => 'c.name']);
        $query->leftJoin(['b' => Consumables::tableName()],'{{a}}.consumables_id = {{b}}.id');
        $query->leftJoin(['c' => Tag::tableName()],'{{b}}.tag_id = {{c}}.id');
        $query->where(['a.id' => $id,'a.spot_id' => $this->spotId]);
        return $query->one();
    }
}
