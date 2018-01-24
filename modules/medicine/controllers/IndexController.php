<?php

namespace app\modules\medicine\controllers;

use Yii;
use app\modules\medicine\models\MedicineDescription;
use app\modules\medicine\models\search\MedicineDescriptionSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\base\Object;
use app\modules\medicine\models\MedicineItem;
use yii\db\Query;
use yii\helpers\Url;
/**
 * IndexController implements the CRUD actions for MedicineDescription model.
 */
class IndexController extends BaseController
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
                    'delete-item' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all MedicineDescription models.
     * @return mixed
     */
    public function actionIndex()
    {    
        $searchModel = new MedicineDescriptionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single MedicineDescription model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "MedicineDescription #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('编辑',['class'=>'btn btn-default btn-form','type'=>"submit"]).
                               Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"])
                ];    
        }else{
            return $this->render('view',[
                'model' => $this->findModel($id)
            ]);
        }
    }

    /**
     * Creates a new MedicineDescription model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($id = null)
    {
        $request = Yii::$app->request;
        $model = new MedicineDescription();  

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet && $id == null){
                return [
                    'title'=> "新增-药品",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                    Html::button('保存，下一步',['class'=>'btn btn-default btn-form','type'=>"submit"])
        
                ];         
               
            }else if($id == null && $model->load($request->post()) && $model->save()){
                return $this->createItem($model->id);                
            }else if($id){//新增用药指南-使用指征入口
                return $this->createItem($id);
            }else{           
                return [
                    'title'=> "新增-药品",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                               Html::button('保存，下一步',['class'=>'btn btn-default btn-form','type'=>"submit"])
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
     * @return 保存用药指南-使用指征内容
     * @param 用药指南id $id
     */
    protected function createItem($id){
        $request = Yii::$app->request;
        $itemModel = new MedicineItem();
        $itemModel->medicine_description_id = $id;
        if ($request->isAjax) {
        
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                //新增用药指南名称入口
                return [
                    'title'=> "新增-用药指南",
                    'content'=>$this->renderAjax('create-item', [
                        'model' => $itemModel,
                        'id' => $id
                    ]),
                    'forceReload' => '#crud-datatable-pjax',
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                    Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
                
                ];
            }else if ($itemModel->load(Yii::$app->request->post()) && $itemModel->save()) {
                $model = $this->findMedicineItem($id);
                $medicineItemList = MedicineItem::find()->select(['id','indication'])->where(['medicine_description_id' => $id])->orderBy(['id' => SORT_DESC])->asArray()->all();
                $options = [
                    'data-confirm'=>false,
                    'data-method'=>false,
                    'data-request-method'=>'post',
                    'role'=>'modal-remote',
                    'data-toggle'=>'tooltip',
                    //                     'data-confirm-title'=>'系统提示',
                    'data-delete' => true,
                    //                     'data-confirm-message'=>Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'class'=>'btn btn-cancel btn-form',
                    'id' => 'delete-item',
                ];
                return [
                    'title'=> "编辑-用药指南",
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'id' => $id,
                        'medicineItemList' => $medicineItemList
                    ]),
                    'forceMessage' => 'true',
                    'footer'=> !$model->isNewRecord?Html::a('删除当前指征',['@medicineIndexDeleteItem','id' => $model->id],$options).
                               Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                               Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"]):''
                ];
            } else {
                //新增用药指南名称入口
                return [
                    'title'=> "新增-用药指南",
                    'content'=>$this->renderAjax('create-item', [
                        'model' => $itemModel,
                        'id' => $id
                    ]),
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                    Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
                
                ];
            }
        }
    }
    /**
     * Updates an existing MedicineDescription model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            $medicineItemList = MedicineItem::find()->select(['id','indication'])->where(['medicine_description_id' => $id])->orderBy(['id' => SORT_DESC])->asArray()->all();
            if($request->isGet){
                $model = $this->findMedicineItem($id);
                $options = [
                    'data-confirm'=>false,
                    'data-method'=>false,
                    'data-request-method'=>'post',
                    'role'=>'modal-remote',
                    'data-toggle'=>'tooltip',
//                     'data-confirm-title'=>'系统提示',
                    'data-delete' => true,
//                     'data-confirm-message'=>Yii::t('yii', 'Are you sure you want to delete this item?'),
                    'class'=>'btn btn-cancel btn-form',
                    'id' => 'delete-item',
                ];
                return [
                    
                    'title'=> "编辑-用药指南",
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'id' => $id,
                        'medicineItemList' => $medicineItemList
                    ]),
                    'footer'=> !$model->isNewRecord?Html::a('删除当前指征',['@medicineIndexDeleteItem','id' => $model->id],$options).
                               Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                               Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"]):''
                ];         
            }else if($request->post()){
                $itemModel = $this->findItemModel($request->post()['MedicineItem']['id']);
                if($itemModel->load($request->post()) && $itemModel->save()){
                    return [
                        'forceReload'=>'#crud-datatable-pjax',
                        'forceClose' => true,
                        'forceMessage' => 'true'
                    ];   
                }
            }else{
                 return [
                     
                    'title'=> "编辑-用药指南",
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                        'id' => $id,
                        'medicineItemList' => $medicineItemList
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
     * Delete an existing MedicineDescription model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
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
    public function actionDeleteItem($id){
        $request = Yii::$app->request;
        if($request->isAjax){
            /*
             *   Process for ajax request
             */
            $this->findItemModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'forceClose'=>true,
                'forceReload'=>'#crud-datatable-pjax',
                'forceMessage' => '删除成功',
            ];
        }else{
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['index']);
        }
    }
    /**
     * @return 根据使用指征id获取对应信息
     * @param 使用指征表id $id
     * @throws NotFoundHttpException
     */
    protected function findItemModel($id){
        if (($model = MedicineItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
    /**
     * @return 返回用药指南关联的使用指征信息
     * @param 用药指南id $id
     */
    protected function findMedicineItem($id){
        if (($model = MedicineItem::find()->where(['medicine_description_id' => $id])->orderBy(['id' => SORT_DESC])->one()) !== null) {
            return $model;
        } else {
            return new MedicineItem();
        }
    }
    /**
     * Finds the MedicineDescription model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MedicineDescription the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MedicineDescription::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
