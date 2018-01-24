<?php

namespace app\modules\spot_set\controllers;

use app\common\base\MultiModel;
use app\modules\spot\models\CureList;
use Yii;
use yii\db\Query;
use yii\helpers\Html;
use yii\web\Response;
use app\modules\spot_set\models\ClinicCure;
use app\modules\spot_set\models\search\ClinicCureSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\ActiveQuery;
use app\modules\spot\models\Tag;

/**
 * ClinicCureListController implements the CRUD actions for ClinicCure model.
 */
trait ClinicCureTrait
{

    /**
     * Lists all ClinicCure models.
     * @return mixed
     */
    public function actionCureClinicIndex()
    {
        $searchModel = new ClinicCureSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams,$this->pageSize);

        return $this->render('clinic-cure/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ClinicCure model.
     * @param integer $id
     * @return mixed
     */
    public function actionCureClinicView($id)
    {
        $model = $this->findActiveQueryModel($id);

        return $this->render('clinic-cure/view', [
            'model' => $model
        ]);
    }

    /**
     * Creates a new ClinicCure model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCureClinicCreate()
    {
        $model = new ClinicCure();
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'forceClose' => true,
                'forceReload' => '#crud-datatable-pjax',
                'forceMessage' => '操作成功'
            ];
        } else {
            $parentCureList = CureList::getValidCureList(0);
            return [
                'title' => "新增治疗医嘱",
                'content' => $this->renderAjax('clinic-cure/_form', [
                    'model' => $model,
                    'parentCureList' => $parentCureList,
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-card btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn  btn-default btn-form ', 'type' => "submit"])
            ];
        }
    }

    /**
     * Updates an existing ClinicCure model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionCureClinicUpdate($id)
    {
        $model = $this->findActiveQueryModel($id);
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return [
                'forceClose' => true,
                'forceReload' => '#crud-datatable-pjax',
                'forceMessage' => '操作成功'
            ];
        } else {
            return [
                'title' => "修改治疗医嘱",
                'content' => $this->renderAjax('clinic-cure/_form', [
                    'model' => $model,
                ]),
                'footer' => Html::button('取消', ['class' => 'btn btn-card btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn  btn-default btn-form ', 'type' => "submit"])
            ];
        }
    }

    /**
     * Delete an existing ClinicCure model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    
    public function actionCureClinicDelete($id)
    {
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            $this->findCureModel($id)->delete();
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['clinic-cure-index']);
        }
    }
    
    /**
     * Finds the ClinicCure model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ClinicCure the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findCureModel($id)
    {
        if (($model = ClinicCure::findOne(['id' => $id,'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
    
    protected function findActiveQueryModel($id){
        $query = new ActiveQuery(ClinicCure::className());
        $query->from(['a' => ClinicCure::tableName()]);
        $query->select(['a.id','a.cure_id','a.default_price','a.price','a.create_time','b.update_time','b.name','b.unit','b.meta','b.remark','b.international_code','b.tag_id','b.status','tag_name' => 'c.name']);
        $query->leftJoin(['b' => CureList::tableName()],'{{a}}.cure_id = {{b}}.id');
        $query->leftJoin(['c' => Tag::tableName()],'{{b}}.tag_id = {{c}}.id');
        $query->where(['a.id' => $id,'a.spot_id' => $this->spotId]);
        return $query->one();
    }
}
