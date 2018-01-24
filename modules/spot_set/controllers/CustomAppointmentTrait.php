<?php

namespace app\modules\spot_set\controllers;

use app\modules\spot_set\models\ThirdPlatform;
use Yii;
use app\modules\spot_set\models\SpotType;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use app\modules\spot_set\models\search\SpotTypeSearch;


/**
 * CustomAppointmentController implements the CRUD actions for spotType model.
 */
trait CustomAppointmentTrait
{

    /**
     * Lists all spotType models.
     * @return mixed
     */
    public function actionCustomAppointmentIndex()
    {    
        $searchModel = new SpotTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);
        $platformNameList = SpotType::getThirdPlatformByList($dataProvider->keys);
        return $this->render('custom-appointment/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'platformNameList' => $platformNameList,
        ]);
    }


    /**
     * Displays a single spotType model.
     * @param string $id
     * @return mixed
     */
    public function actionCustomAppointmentView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "spotType #".$id,
                    'content'=>$this->renderAjax('custom-appointment/view', [
                        'model' => $this->findCustomAppointmentModel($id),
                    ]),
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                    		   Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
                ];    
        }else{
            return $this->render('custom-appointment/view',[
                'model' => $this->findCustomAppointmentModel($id)
            ]);
        }
    }

    /**
     * Creates a new spotType model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCustomAppointmentCreate()
    {
        $request = Yii::$app->request;
        $model = new SpotType();
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                $model->thirdPlatform=1;//第三方平台默认勾选妈咪知道APP
                return [
                    'title'=> "新增服务类型",
                    'content'=>$this->renderAjax('custom-appointment/create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                               Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->validate()){


                $model->time = SpotType::$getTime[$model->time];
                $model->save();

                /*
             * 根据选择的第三方平台添加到数据库里面
             */
                $id = $model->attributes['id'];
                  if($model->thirdPlatform) {    //如果选择了第三方平台，则现在选择的替换原来的选择或者增加选择的
                      $data = [];
                      foreach ($model->thirdPlatform as $key => $v) {
                          $data[$key]['platform_id'] = $v;
                          $data[$key]['spot_id'] = $this->spotId;
                          $data[$key]['spot_type_id'] = $id;
                          $data[$key]['create_time'] = time();
                          $data[$key]['update_time'] = time();
                      }
                      $insertField = ['platform_id', 'spot_id', 'spot_type_id', 'create_time', 'update_time'];
                      Yii::$app->db->createCommand()->batchInsert(ThirdPlatform::tableName(), $insertField, $data)->execute();
                  }
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'forceClose' => true
                ];
            }else{
                return [
                    'title'=> "新增服务类型",
                    'content'=>$this->renderAjax('custom-appointment/create', [
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
    public function actionCustomAppointmentUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findCustomAppointmentModel($id);    
        $model->scenario = 'custom';
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            
            if($request->isGet){
                $model->thirdPlatform=SpotType::getThirdPlatform($model->id)['id'];
                return [
                    'title'=> "编辑服务类型",
                    'content'=>$this->renderAjax('custom-appointment/update', [
                        'model' => $model,
                    ]),
                    'footer'=>  Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                                Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->validate()){

                /*
                 * 根据选择的第三方预约平台生成记录
                 */
                ThirdPlatform::deleteAll(['spot_type_id'=>$id]);
                if($model->thirdPlatform) {    //如果选择了第三方平台，则现在选择的替换原来的选择或者增加选择的
                        $data = [];
                        foreach ($model->thirdPlatform as $key => $v) {
                            $data[$key]['platform_id'] = $v;
                            $data[$key]['spot_id'] = $this->spotId;
                            $data[$key]['spot_type_id'] = $id;
                            $data[$key]['create_time'] = time();
                            $data[$key]['update_time'] = time();
                        }
                        $insertField = ['platform_id', 'spot_id', 'spot_type_id', 'create_time', 'update_time'];
                        Yii::$app->db->createCommand()->batchInsert(ThirdPlatform::tableName(), $insertField, $data)->execute();
                }

                $model->time = SpotType::$getTime[$model->time];
                $model->save();
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'forceClose' => true
                ];
            }else{
                 return [
                    'title'=> "编辑服务类型",
                    'content'=>$this->renderAjax('custom-appointment/update', [
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
    public function actionCustomAppointmentDelete($id)
    {

        $request = Yii::$app->request;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            $this->findCustomAppointmentModel($id)->delete();
            ThirdPlatform::deleteAll(['spot_type_id'=>$id]);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['custom-appointment-index']);
        }      

    }

    /**
     * Finds the spotType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return spotType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCustomAppointmentModel($id)
    {
        if (($model = SpotType::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }


}
