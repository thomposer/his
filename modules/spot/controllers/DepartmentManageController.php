<?php

namespace app\modules\spot\controllers;

use Yii;
use yii\web\Response;
use app\modules\spot\models\OnceDepartment;
use app\modules\spot\models\SecondDepartment;
use app\modules\spot\models\search\OnceDepartmentSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;


class DepartmentManageController extends BaseController
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'second-department-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all OnceDepartment models.
     *  * @desc 机构下科室管理的展示
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OnceDepartmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);

        return $this->render('/department-manage/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return array
     * @desc 机构下新增一级科室
     */
    public function actionOnceDepartmentCreate(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new OnceDepartment();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'forceClose' => true,
                'forceReload' => '#crud-datatable-pjax',
                'forceMessage' => '保存成功',
            ];
        } else {
            return [
                'title' => "新增一级科室",
                'content' => $this->renderAjax('/once-department/create', [
                    'model' => $model,
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-card btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn  btn-default btn-form ', 'type' => "submit"])
            ];
        }
    }


    /**
     * @return array
     * @desc 机构下修改一级科室
     */
    public function actionOnceDepartmentUpdate($id){
        $model = $this->findOnceDepartmentModel($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'forceClose' => true,
                'forceReload' => '#crud-datatable-pjax',
                'forceMessage' => '保存成功',
            ];
        } else {
            return [
                'title' => "编辑一级科室",
                'content' => $this->renderAjax('/once-department/update', [
                    'model' => $model
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-card btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn  btn-default btn-form ', 'type' => "submit"])
            ];
        }
    }

    /**
     * @param $id 一级科室id
     * @return static
     * @throws NotFoundHttpException
     */
    protected function findOnceDepartmentModel($id){
        if (($model = OnceDepartment::findOne(['id' => $id,'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }


    /**
     * @return array
     * @desc 机构下新增二级科室
     */
    public function actionSecondDepartmentCreate(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = new SecondDepartment();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'forceClose' => true,
                'forceReload' => '#crud-datatable-pjax',
                'forceMessage' => '保存成功',
            ];
        } else {
            $onceDepartmentInfo = OnceDepartment::find()->select(['id','name'])->where(['spot_id' => $this->parentSpotId])->asArray()->all();
            return [
                'title' => "新增二级科室",
                'content' => $this->renderAjax('/second-department/create', [
                    'model' => $model,
                    'onceDepartmentInfo' => $onceDepartmentInfo
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-card btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn  btn-default btn-form ', 'type' => "submit"])
            ];
        }
    }

    /**
     * @return array
     * @desc 机构下修改二级科室
     */
    public function actionSecondDepartmentUpdate($id){
        $model = $this->findSecondDepartmentModel($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'forceClose' => true,
                'forceReload' => '#crud-datatable-pjax',
                'forceMessage' => '保存成功',
            ];
        } else {
            $onceDepartmentInfo = OnceDepartment::find()->select(['id','name'])->where(['spot_id' => $this->parentSpotId])->asArray()->all();
            return [
                'title' => "编辑二级科室",
                'content' => $this->renderAjax('/second-department/update', [
                    'model' => $model,
                    'onceDepartmentInfo' => $onceDepartmentInfo
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-card btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn  btn-default btn-form ', 'type' => "submit"])
            ];
        }
    }


    public function actionSecondDepartmentDelete($id){
        $request = Yii::$app->request;
        if($request->isAjax){
            $model = $this->findSecondDepartmentModel($id);
            $model->status = $model->status == 1 ? 2 : 1;
            $model->save();
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
     * @param $id 二级科室id
     * @return static
     * @throws NotFoundHttpException
     */
    protected function findSecondDepartmentModel($id){
        if (($model = SecondDepartment::findOne(['id' => $id,'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }







}
