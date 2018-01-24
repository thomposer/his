<?php

namespace app\modules\spot\controllers;

use Yii;
use app\modules\spot\models\OrganizationType;
use yii\data\ActiveDataProvider;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use app\modules\spot\models\search\OrganizationTypeSearch;
use app\modules\spot_set\models\SpotType;

/**
 * CustomAppointmentController implements the CRUD actions for OrganizationType model.
 */
class CustomAppointmentController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all OrganizationType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrganizationTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single OrganizationType model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "OrganizationType #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                    		   Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
                ];    
        }else{
            return $this->render('view',[
                'model' => $this->findModel($id)
            ]);
        }
    }

    /**
     * Creates a new spotType model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new OrganizationType();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "新增服务类型",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                               Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->validate()){
                $model->time = OrganizationType::$getTime[$model->time];
                $model->save();
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'forceClose' => true
        
                ];         
            }else{           
                return [
                    'title'=> "新增服务类型",
                    'content'=>$this->renderAjax('create', [
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
     * Updates an existing spotType model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {

        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->scenario = 'custom';

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;

            if($request->isGet){
                return [
                    'title'=> "编辑服务类型",
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=>  Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                               Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
                ];
            }else if($model->load($request->post()) && $model->validate()){
                $model->time = OrganizationType::$getTime[$model->time];
                $model->save();
                $spotType = new SpotType();
                $spotType->updateAll(['type' => $model->name, 'status' => $model->status],['organization_type_id' => $model->id]);
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'forceClose' => true
                ];
            }else{
                 return [
                    'title'=> "编辑服务类型",
                    'content'=>$this->renderAjax('update', [
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
     * Delete an existing spotType model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {

        $request = Yii::$app->request;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            $this->findModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }      

    }

    /**
     * Finds the OrganizationType  model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return OrganizationType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = OrganizationType::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
