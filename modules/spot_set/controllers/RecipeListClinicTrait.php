<?php

namespace app\modules\spot_set\controllers;

use app\modules\medicine\models\MedicineDescription;
use app\modules\spot\models\RecipeList;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\Response;
use app\modules\spot_set\models\RecipelistClinic;
use app\modules\spot_set\models\search\RecipelistClinicSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\spot\models\Tag;

/**
 * RecipeListClinicController implements the CRUD actions for RecipelistClinic model.
 */
trait RecipeListClinicTrait
{

    /**
     * Lists all RecipelistClinic models.
     * @return mixed
     */
    public function actionRecipeClinicIndex() {
        $searchModel = new RecipelistClinicSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
//        var_dump($dataProvider);
        return $this->render('recipe-list-clinic/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RecipelistClinic model.
     * @param integer $id
     * @return mixed
     */
    public function actionRecipeClinicView($id) {
        $model = $this->findRecipeActiveQueryModel($id);

        foreach (explode(',', $model->dose_unit) as $key => $val) {
            $does_unit[] = RecipeList::$getDoseUnit[$val];
        }
        $model->dose_unit = implode(',', $does_unit);

        return $this->render('recipe-list-clinic/view', [
                    'model' => $model,
        ]);
    }

    /**
     * Creates a new RecipelistClinic model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionRecipeClinicCreate() {
        $request = Yii::$app->request;
        $model = new RecipelistClinic();
        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceClose' => true,
                    'forceReload' => '#crud-datatable-pjax',
                    'forceMessage' => '保存成功'
                ];
            } else {
                $recipeList = RecipeList::getListByClinicUnion(['a.status' => 1]);
                return [
                    'title' => "新增处方医嘱",
                    'content' => $this->renderAjax('recipe-list-clinic/create', [
                        'model' => $model,
                        'recipeList' => $recipeList
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确认', ['class' => 'btn btn-default btn-form', 'type' => "submit"]),
                ];
            }
        } else {
            /*
             *   Process for non-ajax request
             */
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * Updates an existing RecipelistClinic model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRecipeClinicUpdate($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findRecipeActiveQueryModel($id);
            $shelves = explode('-', $model->shelves);
            $model->shelves1 = $shelves[0];
            $model->shelves2 = $shelves[1];
            $model->shelves3 = $shelves[2];
            
            if ($model->load($request->post()) && $model->save()) {

                return [
                    'forceClose' => true,
                    'forceReload' => '#crud-datatable-pjax',
                    'forceMessage' => '保存成功'
                ];
            } else {
                $fields = ['id', 'name', 'specification', 'unit', 'manufactor', 'price', 'default_price', 'remark', 'medicine_description_id', 'dose_unit', 'drug_type', 'type', 'high_risk'];
                $recipeList = RecipeList::getList($fields);
                $model->dose_unit = explode(',', $model->dose_unit);
                return [
                    'title' => "修改处方医嘱",
                    'content' => $this->renderAjax('recipe-list-clinic/create', [
                        'model' => $model,
                        'recipeList' => $recipeList
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确认', ['class' => 'btn btn-default btn-form', 'type' => "submit"]),
                ];
            }
        } else {
            /*
             *   Process for non-ajax request
             */
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * Delete an existing RecipelistClinic model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRecipeClinicDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            $this->findRecipeModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['recipe-list-clinic-index']);
        }
    }

    /**
     * @param $id
     * 查询处方数据
     */
    protected function findRecipeActiveQueryModel($id) {
        $query = new ActiveQuery(RecipelistClinic::className());
        $query->from(['a' => RecipelistClinic::tableName()]);
        $query->leftJoin(['b' => RecipeList::tableName()], '{{a}}.recipelist_id={{b}}.id');
        $query->leftJoin(['c' => Tag::tableName()], '{{b}}.tag_id = {{c}}.id');
        $query->select([ 'a.*', 'b.name', 'b.meta', 'b.default_used', 'b.default_consumption', 'b.skin_test_status', 'b.skin_test', 'b.remark', 'b.drug_type', 'b.specification', 'b.type', 'b.dose_unit', 'b.unit', 'b.manufactor', 'b.high_risk', 'b.product_name', 'b.en_name', 'b.insurance', 'b.app_number', 'b.import_regist_no', 'b.international_code', 'tag_name' => 'c.name']);
        $query->where(['a.spot_id' => $this->spotId, 'a.id' => $id]);
        $model = $query->one();
        if ($model != null) {
            return $model;
        }
        throw new NotFoundHttpException('你所请求的页面不存在');
    }

    /**
     * Finds the RecipelistClinic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RecipelistClinic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findRecipeModel($id) {
        if (($model = RecipelistClinic::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }


}
