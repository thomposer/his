<?php

namespace app\modules\spot_set\controllers;

use Yii;
use yii\web\Response;
use app\modules\spot_set\models\InspectClinic;
use app\modules\spot_set\models\search\InspectClinicSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\common\base\BaseController;
use yii\helpers\Html;
use app\modules\spot\models\Inspect;
use app\modules\spot_set\models\InspectItemUnionClinic;
use app\modules\spot\models\Tag;

/**
 * InspectClinicController implements the CRUD actions for InspectClinic model.
 */
trait InspectClinicTrait
{

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'inspect-clinic-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all InspectClinic models.
     * @return mixed
     */
    public function actionInspectClinicIndex() {
        $searchModel = new InspectClinicSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        $inspectList = Inspect::getInspectList();
        return $this->render('inspect-clinic/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'inspectList' => $inspectList
        ]);
    }

    /**
     * Displays a single InspectClinic model.
     * @param string $id
     * @return mixed
     */
    public function actionInspectClinicView($id) {
        return $this->render('inspect-clinic/view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new InspectClinic model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionInspectClinicCreate() {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new InspectClinic();
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $res = $model->save();
                //保存关联项目
                if ($res && $model->item) {
                    $row = [];
                    foreach ($model->item as $key => $value) {
                        $row[] = [$model->inspect_id, $value, $model->id, $this->spotId, time(), time()];
                    }
                    Yii::$app->db->createCommand()->batchInsert(InspectItemUnionClinic::tableName(), ['inspect_id', 'item_id', 'clinic_inspect_id', 'spot_id', 'create_time', 'update_time'], $row)->execute();
                }
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'forceClose' => true,
                    'forceMessage' => '操作成功'
                ];
            } else {
                $inspectList = Inspect::getInspectList(1, 2,1);
                $itemList = !empty($inspectList[$model->inspect_id]['inspectItem']) ? $inspectList[$model->inspect_id]['inspectItem'] : [];
                $ret = [
                    'title' => "新增实验室检查",
                    'content' => $this->renderAjax('inspect-clinic/create', [
                        'model' => $model,
                        'inspectList' => $inspectList,
                        'itemList' => $itemList
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确认', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
                return $ret;
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Updates an existing InspectClinic model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionInspectClinicUpdate($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($id);
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                //保存关联项目
                if ($model->item) {
                    //先删除旧的数据
                    InspectItemUnionClinic::deleteAll(['clinic_inspect_id' => $id]);
                    $row = [];
                    foreach ($model->item as $key => $value) {
                        $row[] = [$model->inspect_id, $value, $model->id, $this->spotId, time(), time()];
                    }
                    Yii::$app->db->createCommand()->batchInsert(InspectItemUnionClinic::tableName(), ['inspect_id', 'item_id', 'clinic_inspect_id', 'spot_id', 'create_time', 'update_time'], $row)->execute();
                }
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'forceClose' => true,
                    'forceMessage' => '操作成功'
                ];
            } else {
                $inspect = Inspect::getInspectById($model->inspect_id);
                $model->inspectUnit = $inspect['inspect_unit'];
                $model->phonetic = $inspect['phonetic'];
                $model->internationalCode = $inspect['international_code'];
                $model->tagId = Tag::getTag($inspect['tag_id'])['name'];
                $model->parentStatus = InspectClinic::$getStatus[$inspect['status']];
                $model->doctorRemark = $inspect['remark'];
                $model->englishName = $inspect['inspect_english_name'];
                $inspectList = Inspect::getInspectList(2, 2);
                $itemListData = InspectClinic::itemtList($model->id);
                if (isset($model->errors['item']) && $model->errors['item']) {
                    $model->item = [];
                } else {
                    $model->item = array_column($itemListData, 'item_id');
                }
                $itemList = !empty($inspectList[$model->inspect_id]['inspectItem']) ? $inspectList[$model->inspect_id]['inspectItem'] : [];
                $ret = [
                    'title' => "编辑实验室检查",
                    'content' => $this->renderAjax('inspect-clinic/update', [
                        'model' => $model,
                        'inspectList' => $inspectList,
                        'itemList' => $itemList
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确认', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
                return $ret;
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @return 关联检验医嘱项目
     */
    public function actionInspectClinicUnion($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($id);
            $model->scenario = 'union';
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                //保存关联项目
                if ($model->item) {
                    //先删除旧的数据
                    InspectItemUnionClinic::deleteAll(['clinic_inspect_id' => $id]);
                    $row = [];
                    foreach ($model->item as $key => $value) {
                        $row[] = [$model->inspect_id, $value, $model->id, $this->spotId, time(), time()];
                    }
                    Yii::$app->db->createCommand()->batchInsert(InspectItemUnionClinic::tableName(), ['inspect_id', 'item_id', 'clinic_inspect_id', 'spot_id', 'create_time', 'update_time'], $row)->execute();
                }
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'forceClose' => true,
                    'forceMessage' => '操作成功'
                ];
            } else {
                $inspectList = Inspect::getInspectList(1, 2);
                $itemListData = InspectClinic::itemtList($id);
                if (isset($model->errors['item']) && $model->errors['item']) {
                    $model->item = [];
                } else {
                    $model->item = array_column($itemListData, 'item_id');
                }
                $itemList = $inspectList[$model->inspect_id]['inspectItem'];
                $ret = [
                    'title' => "关联项目",
                    'content' => $this->renderAjax('inspect-clinic/union', [
                        'model' => $model,
                        'itemList' => $itemList
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('确认', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
                return $ret;
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Delete an existing InspectClinic model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionInspectClinicDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $this->findModel($id)->delete();
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax', 'forceMessage' => '操作成功'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['inspect-clinic-index']);
        }
    }

    /**
     * Finds the InspectClinic model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return InspectClinic the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = InspectClinic::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

}
