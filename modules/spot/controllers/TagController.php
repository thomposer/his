<?php

namespace app\modules\spot\controllers;

use Yii;
use app\modules\spot\models\Tag;
use app\modules\spot\models\search\TagSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use app\modules\spot\models\CureList;
use app\modules\spot\models\Inspect;
use app\modules\spot\models\CheckList;
use app\modules\spot\models\RecipeList;
use yii\db\Query;
use yii\data\ArrayDataProvider;
use app\modules\spot_set\models\Material;
use app\modules\spot\models\CardDiscount;
use app\modules\spot_set\models\CardDiscountClinic;
use app\modules\spot\models\AdviceTagRelation;
use app\modules\spot\models\Consumables;

/**
 * TagController implements the CRUD actions for Tag model.
 */
class TagController extends BaseController
{

    /**
     * @inheritdoc
     */
    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                    'delete-union' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Tag models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new TagSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tag model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
                    'dataProvider' => $this->tagOrdersSearch($id),
        ]);
    }

    /**
     * Creates a new Tag model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $request = Yii::$app->request;
        $model = new Tag();

        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'forceClose' => true
                ];
            } else {
                return [
                    'title' => "新建标签",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
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
     * Updates an existing Tag model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $model->scenario = 'update';
        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'forceClose' => true,
                    'forceMessage' => '保存成功',
                ];
            } else {
                return [
                    'title' => "修改标签",
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                        'haveUnion' => Tag::haveUnion($id),
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
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
     * Delete an existing Tag model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id) {

        $request = Yii::$app->request;
        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            $this->findModel($id)->delete();
            //删除  充值卡配置的折扣信息 
            CardDiscount::deleteAll(['tag_id' => $id, 'spot_id' => $this->parentSpotId]);
            CardDiscountClinic::deleteAll(['tag_id' => $id, 'parent_spot_id' => $this->parentSpotId]);
            
            AdviceTagRelation::deleteAll(['tag_id' => $id, 'spot_id' => $this->parentSpotId]);//删除所有关联项
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Tag model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Tag the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Tag::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    public function tagOrdersSearch($id) {
        $model = $this->findModel($id);
        if($model->type == 1){//充值卡折扣标签关联
            $query = new Query();
            $inspectList = $query->from(['t' => Inspect::tableName()])
                    ->select(['t.inspect_name as name', 't.inspect_price as price', 't.id'])
                    ->where(['t.spot_id' => $this->parentSpotId, 't.tag_id' => $id])
                    ->all();
            $inspectList = $this->addClumn($inspectList, ['type' => Tag::$inspectType, 'ordersName' => '实验室检查']);


            $checkList = $query->from(['t' => CheckList::tableName()])
                    ->select(['t.name', 't.price', 't.id'])
                    ->where(['t.spot_id' => $this->parentSpotId, 't.tag_id' => $id])
                    ->all();
            $checkList = $this->addClumn($checkList, ['type' => Tag::$checkType, 'ordersName' => '影像学检查']);



            $cureList = $query->from(['t' => CureList::tableName()])
                    ->select(['t.name', 't.price', 't.id'])
                    ->where(['t.spot_id' => $this->parentSpotId, 't.tag_id' => $id])
                    ->all();
            $cureList = $this->addClumn($cureList, ['type' => Tag::$cureType, 'ordersName' => '治疗']);

            $recipeList = $query->from(['t' => RecipeList::tableName()])
                    ->select(['t.name', 't.price', 't.id'])
                    ->where(['t.spot_id' => $this->parentSpotId, 't.tag_id' => $id])
                    ->all();
            $recipeList = $this->addClumn($recipeList, ['type' => Tag::$recipeType, 'ordersName' => '处方']);
        
            $materialList = $query->from(['t' => Material::tableName()])
                    ->select(['t.name', 't.price', 't.id'])
                    ->where(['t.spot_id' => $this->spotId, 't.tag_id' => $id])
                    ->all();
            $materialList = $this->addClumn($materialList, ['type' => Tag::$materialType, 'ordersName' => '其他']);

            $consumablesList = $query->from(['t' => Consumables::tableName()])
                    ->select(['t.name', 't.id'])
                    ->where(['t.spot_id' => $this->parentSpotId, 't.tag_id' => $id])
                    ->all();
            $consumablesList = $this->addClumn($consumablesList, ['type' => Tag::$consumablesType, 'ordersName' => '医疗耗材']);

            $data = array_merge($checkList, $inspectList, $cureList, $recipeList, $consumablesList, $materialList);
            $provider = new ArrayDataProvider([
                'allModels' => $data,
                'pagination' => [
                    'pageSize' => 20,
                ],
                'sort' => [
    //                'attributes' => ['name', 'price', 'ordersName'],
                ],
            ]);
            return $provider;
        }else{//通用标签
            $query = new Query();
            $recipeList = $query->from(['a' => AdviceTagRelation::tableName()])
                    ->leftJoin(['r' => RecipeList::tableName()], '{{a}}.advice_id = {{r}}.id')
                    ->select(['r.name', 'r.price', 'r.id'])
                    ->where(['a.spot_id' => $this->parentSpotId, 'a.tag_id' => $id, 'a.type' => AdviceTagRelation::$recipeType])
                    ->all();
            $recipeList = $this->addClumn($recipeList, ['type' => 4, 'ordersName' => '处方']);
            
            $provider = new ArrayDataProvider([
                'allModels' => $recipeList,
                'pagination' => [
                    'pageSize' => 20,
                ],
                'sort' => [
    //                'attributes' => ['name', 'price', 'ordersName'],
                ],
            ]);
            return $provider;
        }
    }

    /*
     * 二维数组的数组元素插入相同元素
     */

    protected function addClumn($array, $field) {
        foreach ($array as &$value) {
            $value = array_merge($value, $field);
        }
        return $array;
    }

    /*
     * 取消关联项目
     */

    public function actionDeleteUnion($tagId, $id, $type) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($tagId);
        if($model->type == 1){//充值卡折扣标签取消关联
            if (Tag::$inspectType == $type && ($inspectModel = Inspect::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== NULL) {
                $inspectModel->tag_id = 0;
                $inspectModel->save();
            } else if (Tag::$checkType == $type && ($checkModel = CheckList::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== NULL) {
                $checkModel->tag_id = 0;
                $checkModel->save();
            } else if (Tag::$cureType == $type && ($cureModel = CureList::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== NULL) {
                $cureModel->tag_id = 0;
                $cureModel->save();
            } else if (Tag::$recipeType == $type && ($recipeModel = RecipeList::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== NULL) {
                $recipeModel->tag_id = 0;
                $recipeModel->save();
            } else if (Tag::$materialType == $type && ($materialModel = Material::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== NULL) {
                $materialModel->tag_id = 0;
                $materialModel->save();
            } else if (Tag::$consumablesType == $type && ($consumablesModel = Consumables::findOne(['id' => $id, 'spot_id' => $this->parentSpotId])) !== NULL){
                $consumablesModel->tag_id = 0;
                $consumablesModel->save();
            }else {
                return [
                    'forceReload' => '#orders-list-pjax',
                    'forceClose' => true,
                    'forceMessage' => '找不到对应的医嘱',
                ];
            }
        }else{
            if($type == AdviceTagRelation::$recipeType){
                AdviceTagRelation::deleteAll(['spot_id' => $this->parentSpotId, 'advice_id' => $id, 'tag_id' => $tagId]);//删除所有关联项
            }else {
                return [
                    'forceReload' => '#orders-list-pjax',
                    'forceClose' => true,
                    'forceMessage' => '找不到对应的医嘱',
                ];
            }
        }

        return [
            'forceReload' => '#orders-list-pjax',
            'forceClose' => true,
            'forceMessage' => '保存成功',
        ];
    }

}
