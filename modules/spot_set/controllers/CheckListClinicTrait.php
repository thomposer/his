<?php

namespace app\modules\spot_set\controllers;

use Yii;
use yii\db\Query;
use yii\web\Response;
use app\modules\spot_set\models\CheckListClinic;
use app\modules\spot_set\models\search\CheckListClinicSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\ActiveQuery;
use app\modules\spot\models\CheckList;
use yii\helpers\Html;
use app\modules\spot\models\Tag;

/**
 * CheckListClinicController implements the CRUD actions for CheckListClinic model.
 */
trait CheckListClinicTrait
{

    /**
     * Lists all CheckListClinic models.
     * @return mixed
     */
    public function actionCheckListClinicIndex()
    {
        $searchModel = new CheckListClinicSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);
        //获取当前机构下的所有检查医嘱
        $checkList = CheckList::getParentSpotCheckList();
        return $this->render('check-list-clinic/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'checkList' => $checkList
        ]);
    }

    /**
     * Displays a single CheckListClinic model.
     * @param integer $id
     * @return mixed
     */
    public function actionCheckListClinicView($id)
    {
        return $this->render('check-list-clinic/view', [
            'model' => $this->findCheckListClinicModel($id),
        ]);
    }

    /**
     * Creates a new CheckListClinic model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCheckListClinicCreate()
    {
        $model = new CheckListClinic();
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'forceClose' => true,
                'forceReload' => '#crud-datatable-pjax',
                'forceMessage' => '保存成功',
            ];
        } else {
            //获取当前机构下的所有检查医嘱
            $checkList = CheckList::getParentSpotCheckList(['a.status'=>1],[1],1);

            return [
                'title' => "新增影像学检查",
                'content' => $this->renderAjax('check-list-clinic/create', [
                    'model' => $model,
                    'checkList' => $checkList,
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-card btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                Html::button('保存', ['class' => 'btn  btn-default btn-form ', 'type' => "submit"])
            ];
        }
    }

    /**
     * Updates an existing CheckListClinic model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionCheckListClinicUpdate($id)
    {
        $model = $this->findCheckListClinicModel($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'forceClose' => true,
                'forceReload' => '#crud-datatable-pjax',
                'forceMessage' => '保存成功',
            ];
        } else {
            //获取当前机构下的所有检查医嘱
            $checkList = CheckList::getParentSpotCheckList(['a.status'=>1]);
            return [
                'title' => "编辑影像学检查",
                'content' => $this->renderAjax('check-list-clinic/update', [
                    'model' => $model,
                    'checkList' => $checkList,
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-card btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn  btn-default btn-form ', 'type' => "submit"])
            ];
        }
    }

    /**
     * Delete an existing CheckListClinic model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    
    public function actionCheckListClinicDelete($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            //判断机构下医嘱的状态
            $this->findCheckModel($id)->delete();
            return ['forceClose' => true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['check-list-clinic-index']);
        }
    }

    /**
     * Finds the CheckListClinic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CheckListClinic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCheckModel($id)
    {
        if (($model = CheckListClinic::findOne(['id' => $id,'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * @param $id 诊所下医嘱id
     * @return array|null|\yii\db\ActiveRecord 医嘱信息
     * @throws NotFoundHttpException
     */
    public function findCheckListClinicModel($id){
        $query = new ActiveQuery(CheckListClinic::className());
        $query->from(['a' => CheckListClinic::tableName()]);
        $query->select([ 'a.id', 'a.check_id','a.price','a.default_price','a.status','b.name','b.unit','b.meta','b.remark','b.international_code','tagName'=>'c.name']);
        $query->leftJoin(['b' => CheckList::tableName()], '{{a}}.check_id = {{b}}.id');
        $query->leftJoin(['c' => Tag::tableName()], '{{b}}.tag_id = {{c}}.id');
        $query->where(['a.id' => $id, 'a.spot_id' => $this->spotId]);
        $model = $query->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

}
