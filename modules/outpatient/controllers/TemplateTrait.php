<?php

namespace app\modules\outpatient\controllers;

/*
 * time: 2017-5-23 17:37:17.
 * author : yu.li.
 * 将模板管理全部抽出来分组  便于管理
 */

use app\modules\spot_set\models\CheckListClinic;
use Yii;
use app\modules\outpatient\models\search\OutpatientCaseTemplateSearch;
use app\modules\spot\models\CaseTemplate;
use yii\web\Response;
use app\modules\spot\models\search\ChildCareTemplateSearch;
use app\modules\spot\models\ChildCareTemplate;
use yii\web\NotFoundHttpException;
use app\modules\outpatient\models\RecipeTypeTemplate;
use app\modules\outpatient\models\search\RecipeTypeTemplateSearch;
use app\modules\outpatient\models\search\RecipeTemplateSearch;
use app\modules\outpatient\models\RecipeTemplate;
use app\common\base\MultiModel;
use app\modules\outpatient\models\RecipeTemplateInfo;
use app\modules\spot\models\RecipeList;
use yii\db\Exception;
use app\modules\outpatient\models\InspectTemplate;
use app\modules\outpatient\models\InspectTemplateInfo;
use app\modules\outpatient\models\search\InspectTemplateSearch;
use app\modules\spot_set\models\InspectClinic;
use yii\data\ActiveDataProvider;
use app\modules\spot_set\models\RecipelistClinic;
use yii\db\Query;
use app\modules\outpatient\models\search\CureTemplateSearch;
use app\modules\outpatient\models\CureTemplate;
use app\modules\outpatient\models\CureTemplateInfo;
use app\modules\spot_set\models\ClinicCure;
use app\modules\outpatient\models\CheckTemplate;
use app\modules\outpatient\models\CheckTemplateInfo;
use app\modules\outpatient\models\search\CheckTemplateSearch;

trait TemplateTrait
{

    /**
     * @return string 医生门诊管理病例模板列表
     */
    public function actionCaseTemplate() {
        $searchModel = new OutpatientCaseTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        return $this->render('template/caseTemplate', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 医生门诊->病历模板->新增
     * Creates a new CaseTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCaseCreateTemplate() {
        $model = new CaseTemplate();
        $model->type = 2;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['case-template']);
        } else {
            return $this->render('template/createCaseTemplate', [
                        'model' => $model,
                        'hidden' => true
            ]);
        }
    }

    /**
     * 医生门诊->病历模板->更新记录
     * Updates an existing CaseTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionCaseUpdateTemplate($id) {
        $model = $this->findTemplateModel($id);
        $model->type = 2;
        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['case-template']);
        } else {
            return $this->render('template/updateCaseTemplate', [
                        'model' => $model,
                        'hidden' => true
            ]);
        }
    }

    /**
     * @param $id 病例模板id
     * @return string 查看病例模板
     * @throws NotFoundHttpException
     */
    public function actionCaseViewTemplate($id) {
        return $this->render('template/caseTemplateView', [
                    'model' => $this->findTemplateModel($id, true),
        ]);
    }

    /**
     * @param $id 病例模板id
     * @return array|Response 删除病例模板
     * @throws NotFoundHttpException
     */
    public function actionCaseDeleteTemplate($id) {

        $request = Yii::$app->request;
        if ($request->isAjax) {

            /*
             *   Process for ajax request
             */
            $model = $this->findTemplateModel($id);
            if ($model->user_id != $this->userInfo->id || $model->type == 1) {
                throw new NotFoundHttpException('你所请求的页面不存在');
            }
            $model->delete();

            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['@outpatientOutpatientCaseTemplate']);
        }
    }

    /**
     * Creates a new CaseTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreateTemplate() {
        $model = new CaseTemplate();
        if ($model->load(Yii::$app->request->post())) {
            if ($model->saveType) {
                //医生门诊保存信息
                return $this->saveCase($model);
            }
        }
    }

    /**
     * ================================儿保模板管理=========================
     */
    public function actionChildIndex() {

        $searchModel = new ChildCareTemplateSearch();
        $params = Yii::$app->request->queryParams;
        $params['source'] = 2;
        $dataProvider = $searchModel->search($params, $this->pageSize);
        return $this->render('template/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CaseTemplate model.
     * @param string $id
     * @return mixed
     */
    public function actionChildView($id) {
        return $this->render('template/view', [
                    'model' => $this->findChildCareModel($id, true),
        ]);
    }

    /**
     * Creates a new CaseTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionChildCreate() {
        $model = new ChildCareTemplate();
        $model->type = 2;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['child-index']);
        } else {
            return $this->render('template/create', [
                        'model' => $model,
                        'hidden' => true
            ]);
        }
    }

    /**
     * Updates an existing CaseTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionChildUpdate($id) {
        $model = $this->findChildCareModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['child-index']);
        } else {
            return $this->render('template/update', [
                        'model' => $model,
                        'hidden' => true
            ]);
        }
    }

    /**
     * Deletes an existing CaseTemplate model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionChildDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $this->findChildCareModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['child-index']);
        }
    }

    /**
     * Lists all RecipeTypeTemplate models.
     * 处方模板分类列表
     * @return mixed
     */
    public function actionRecipeTypeIndex() {
        $searchModel = new RecipeTypeTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('template/recipeTypeTemplate', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * 处方模板分类分类
     * @return mixed
     */
    public function actionRecipeTypeCreate() {
        $model = new RecipeTypeTemplate();
        $model->type = 2;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['recipe-type-index']);
        } else {
            return $this->render('template/createRecipeTypeTemplate', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * 修改处方模板分类
     * @return mixed
     */
    public function actionRecipeTypeUpdate($id) {
        $model = $this->findRecipeTypeModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['recipe-type-index']);
        } else {
            return $this->render('template/updateRecipeTypeTemplate', [
                        'model' => $model,
            ]);
        }
    }

    /**
     *  * 删除处方模板分类
     * Deletes an existing CaseTemplate model.
     * @param string $id
     * @return mixed
     */
    public function actionRecipeTypeDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $this->findRecipeTypeModel($id)->delete();
            RecipeTemplate::updateAll(['recipe_type_template_id' => 0], ['recipe_type_template_id' => $id, 'spot_id' => $this->spotId]);
            InspectTemplate::updateAll(['template_type_id' => 0], ['template_type_id' => $id, 'spot_id' => $this->spotId]);
            CureTemplate::updateAll(['template_type_id' => 0], ['template_type_id' => $id, 'spot_id' => $this->spotId]);
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['recipe-type-index']);
        }
    }

    protected function saveCase($model) {
        if ($model->saveType == 1 && $model->caseId) { //更新
            $model = $this->findTemplateModel($model->caseId);
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->save()) {
                $this->result['errorCode'] = 0;
                $this->result['msg'] = '保存成功';
            } else {
                $this->result['errorCode'] = 1001;
                $errors = $model->getErrors('name');
                $this->result['msg'] = $errors ? $errors[0] : '操作失败';
            }
        } else {
            $this->result['errorCode'] = 1002;
            $errors = $model->getErrors('name');
            $this->result['msg'] = $errors ? $errors[0] : '操作失败';
        }
        return \yii\helpers\Json::encode($this->result);
    }

    /*
     * 渲染  存为病例模板
     */

    protected function viewCaseTemplate() {
        Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'title' => "存为病历模板",
            'content' => $this->renderAjax('_caseForm', [
                'model' => new CaseTemplate(),
            ]),
//            'footer' => Html::button('保存', ['class' => 'btn btn-default union_submit btn-form', 'type' => "button", 'id' => 'union_submit']) .
//            Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"])
        ];
    }

    /**
     * Finds the CaseTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @param string $isView 是否为查看
     * @return CaseTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findTemplateModel($id, $isView = false) {
        $where = ['id' => $id, 'spot_id' => $this->parentSpotId];
        if (!$isView) {
            $where['user_id'] = $this->userInfo->id;
        }
        if (($model = CaseTemplate::findOne($where)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * 
     * @param type $id
     * @param string $isView 是否为查看
     * @return type 儿保Model
     * @throws NotFoundHttpException
     */
    protected function findChildCareModel($id, $isView = false) {
        $where = ['id' => $id, 'spot_id' => $this->parentSpotId];
        if (!$isView) {
            $where['operating_id'] = $this->userInfo->id;
        }
        if (($model = ChildCareTemplate::findOne($where)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     *
     * @param type $id
     * @return type 处方模板分类Model
     * @throws NotFoundHttpException
     */
    protected function findRecipeTypeModel($id) {
        $where = ['id' => $id, 'spot_id' => $this->spotId, 'user_id' => $this->userInfo->id];
        if (($model = RecipeTypeTemplate::findOne($where)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * Lists all RecipeTemplate models.
     * @return mixed
     * ================================处方模板管理=========================
     */
    public function actionRecipetemplateIndex() {
        $searchModel = new RecipeTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('template/recipe-template', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new RecipeTemplate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionRecipetemplateCreate() {
        $model = new MultiModel([
            'models' => [
                'recipeTemplate' => new RecipeTemplate(),
                'recipeTemplateInfo' => new RecipeTemplateInfo()
            ]
        ]);
        $request = Yii::$app->request;
        if ($request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->getModel('recipeTemplate')->type = 2;
            if ($model->load($request->post()) && $model->validate()) {

                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $recipeTemplateInfoModel = $model->getModel('recipeTemplateInfo');
                    if (isset($recipeTemplateInfoModel->recipe_id)) {
                        $result = $model->getModel('recipeTemplate')->save();
                        if ($result) {
                            $rows = [];
                            foreach ($recipeTemplateInfoModel->recipe_id as $key => $v) {
                                if ($recipeTemplateInfoModel->deleted[$key] == null) {
                                    $info = json_decode($v, true);
                                    $rows[] = [
                                        $this->spotId,
                                        $info['recipelist_id'],
                                        $info['id'],
                                        $model->getModel('recipeTemplate')->id,
                                        $recipeTemplateInfoModel->dose[$key],
                                        $recipeTemplateInfoModel->dose_unit[$key],
                                        $recipeTemplateInfoModel->used[$key],
                                        $recipeTemplateInfoModel->frequency[$key],
                                        $recipeTemplateInfoModel->day[$key],
                                        $recipeTemplateInfoModel->num[$key],
                                        $recipeTemplateInfoModel->description[$key],
                                        $recipeTemplateInfoModel->type[$key],
                                        ($recipeTemplateInfoModel->skin_test_status[$key] ? $recipeTemplateInfoModel->skin_test_status[$key] : 0),
                                        $info['skin_test'],
                                        $recipeTemplateInfoModel->curelist_id[$key],
                                        time(),
                                        time()
                                    ];
                                }
                            }
                            if (count($rows) > 0) {
                                Yii::$app->db->createCommand()->batchInsert(RecipeTemplateInfo::tableName(), [
                                    'spot_id', 'recipe_id', 'clinic_recipe_id', 'recipe_template_id', 'dose', 'dose_unit', 'used',
                                    'frequency', 'day', 'num', 'description', 'type', 'skin_test_status', 'skin_test', 'curelist_id',
                                    'create_time', 'update_time'
                                        ], $rows)->execute();
                            }
                        }
                    } else {
                        $this->result['errorCode'] = true;
                        $this->result['msg'] = '请选择处方医嘱';
                        return $this->result;
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
                } catch (Exception $e) {
                    Yii::error(json_encode($e->errorInfo, true), 'recipetemplateCreate');
                    $dbTrans->rollBack();
                }
            } else {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = $model->errors['recipeTemplate'][0][0] ? $model->errors['recipeTemplate'][0][0] : $model->errors['recipeTemplateInfo'][0][0];
                return $this->result;
            }
        } else {
            $where = [
                'user_id' => $this->userInfo->id
            ];
            $type = RecipeTypeTemplate::getList(['id', 'name'], $where);
            $recipetList = RecipelistClinic::getReciptListByStock();
            return $this->render('template/createRecipeTemplate', [
                        'model' => $model,
                        'type' => $type,
                        'recipetList' => $recipetList
            ]);
        }
    }

    /**
     * Updates an existing RecipeTemplate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRecipetemplateUpdate($id) {
        $model = new MultiModel([
            'models' => [
                'recipeTemplate' => $this->findRecipeTemplateModel($id),
                'recipeTemplateInfo' => new RecipeTemplateInfo()
            ]
        ]);
        $request = Yii::$app->request;
        if ($request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->validate()) {

                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $db = Yii::$app->db;
                    $recipeTemplateInfoModel = $model->getModel('recipeTemplateInfo');
                    if (isset($recipeTemplateInfoModel->recipe_id)) {
                        $result = $model->getModel('recipeTemplate')->save();
                        if ($result) {
                            $rows = [];
                            foreach ($recipeTemplateInfoModel->recipe_id as $key => $v) {
                                $list = json_decode($v, true);
                                if (isset($list['isNewRecord']) && $list['isNewRecord'] === 0) {
                                    if ($recipeTemplateInfoModel->deleted[$key] == 1) {
                                        $db->createCommand()->delete(RecipeTemplateInfo::tableName(), ['id' => $list['id'], 'spot_id' => $this->spotId])->execute();
                                    } else {
                                        $skinTest = RecipeList::find()->select(['skin_test'])->where(['id' => $list['recipe_id']])->asArray()->one()['skin_test'];
                                        if (!$skinTest) {
                                            $skinTest = '';
                                        }
                                        RecipeTemplateInfo::updateAll([
                                            'dose' => $recipeTemplateInfoModel->dose[$key],
                                            'dose_unit' => $recipeTemplateInfoModel->dose_unit[$key],
                                            'frequency' => $recipeTemplateInfoModel->frequency[$key],
                                            'day' => $recipeTemplateInfoModel->day[$key],
                                            'num' => $recipeTemplateInfoModel->num[$key],
                                            'description' => $recipeTemplateInfoModel->description[$key],
                                            'type' => $recipeTemplateInfoModel->type[$key],
                                            'used' => $recipeTemplateInfoModel->used[$key],
                                            'skin_test_status' => $recipeTemplateInfoModel->skin_test_status[$key] ? $recipeTemplateInfoModel->skin_test_status[$key] : 0,
                                            'skin_test' => !empty($recipeTemplateInfoModel->skin_test_status[$key]) ? $skinTest : '',
                                            'curelist_id' => !empty($recipeTemplateInfoModel->skin_test_status[$key]) ? $recipeTemplateInfoModel->curelist_id[$key] : 0,
                                            'update_time' => time(),
                                                ], ['id' => $list['id'], 'spot_id' => $this->spotId]);
                                    }
                                } else {
                                    if ($recipeTemplateInfoModel->deleted[$key] == null) {

                                        $rows[] = [
                                            $this->spotId,
                                            $list['recipelist_id'],
                                            $list['id'],
                                            $model->getModel('recipeTemplate')->id,
                                            $recipeTemplateInfoModel->dose[$key],
                                            $recipeTemplateInfoModel->dose_unit[$key],
                                            $recipeTemplateInfoModel->used[$key],
                                            $recipeTemplateInfoModel->frequency[$key],
                                            $recipeTemplateInfoModel->day[$key],
                                            $recipeTemplateInfoModel->num[$key],
                                            $recipeTemplateInfoModel->description[$key],
                                            $recipeTemplateInfoModel->type[$key],
                                            $recipeTemplateInfoModel->skin_test_status[$key] ? $recipeTemplateInfoModel->skin_test_status[$key] : 0,
                                            $list['skin_test'],
                                            $recipeTemplateInfoModel->curelist_id[$key],
                                            time(),
                                            time()
                                        ];
                                    }
                                }
                            }
                            if (count($rows) > 0) {
                                Yii::$app->db->createCommand()->batchInsert(RecipeTemplateInfo::tableName(),
                                    [
                                        'spot_id', 'recipe_id', 'clinic_recipe_id', 'recipe_template_id', 'dose', 'dose_unit', 'used',
                                        'frequency','day','num','description','type','skin_test_status','skin_test','curelist_id',
                                        'create_time','update_time'
                                    ], $rows)->execute();
                            }
                        }
                    } else {
                        $this->result['errorCode'] = true;
                        $this->result['msg'] = '请选择处方医嘱';
                        return $this->result;
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
                } catch (Exception $e) {
                    Yii::error(json_encode($e->errorInfo, true), 'recipetemplateCreate');
                    $dbTrans->rollBack();
                }
            } else {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = $model->errors['recipeTemplate'][0][0] ? $model->errors['recipeTemplate'][0][0] : $model->errors['recipeTemplateInfo'][0][0];
                return $this->result;
            }
        } else {
            $where = [
                'user_id' => $this->userInfo->id
                    ];
            $type = RecipeTypeTemplate::getList(['id','name'],$where);
            $recipetList = RecipelistClinic::getReciptListByStock();
            $recipeTemplateInfoDataProvider = $this->findRecipeTemplateInfoDataProvider($id);
            return $this->render('template/updateRecipeTemplate', [
                        'model' => $model,
                        'type' => $type,
                        'recipetList' => $recipetList,
                        'recipeTemplateInfoDataProvider' => $recipeTemplateInfoDataProvider
            ]);
        }
    }

    /**
     * Delete an existing RecipeTemplate model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionRecipetemplateDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            /*
             *   Process for ajax request
             */
            $this->findRecipeTemplateModel($id)->delete();
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
     * Finds the RecipeTemplate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RecipeTemplate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findRecipeTemplateModel($id) {
        if (($model = RecipeTemplate::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }


    // ================================检验模板管理=========================

    /**
     * @return 检验医嘱模板列表
     */
    public function actionInspectTemplateIndex() {
        $searchModel = new InspectTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('template/inspectTemplate', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return 检验医嘱模板新增
     */
    public function actionInspectTemplateCreate() {
        $model = new MultiModel([
            'models' => [
                'inspectTemplate' => new InspectTemplate(),
                'inspectTemplateInfo' => new InspectTemplateInfo()
            ]
        ]);
        $request = Yii::$app->request;
        if ($request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->getModel('inspectTemplate')->type = 2;
            if ($model->load($request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $inspectTemplateInfoModel = $model->getModel('inspectTemplateInfo');
                    if (isset($inspectTemplateInfoModel->clinic_inspect_id)) {
                        $saveStatus = InspectTemplateInfo::saveInfo($model, $inspectTemplateInfoModel);
                        if (!$saveStatus) {
                            $dbTrans->rollBack();
                            $this->result['errorCode'] = 1001;
                            $this->result['msg'] = '请选择检验医嘱';
                            return $this->result;
                        }
                    } else {
                        $this->result['errorCode'] = true;
                        $this->result['msg'] = '请选择检验医嘱';
                        return $this->result;
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
                } catch (Exception $e) {
                    Yii::error(json_encode($e->errorInfo, true), 'inspectTemplateCreate');
                    $dbTrans->rollBack();
                    $this->result['errorCode'] = 1002;
                    $this->result['msg'] = '操作失败';
                    return $this->result;
                }
            } else {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = $model->errors['inspectTemplate'][0][0] ? $model->errors['inspectTemplate'][0][0] : $model->errors['inspectTemplateInfo'][0][0];
                return $this->result;
            }
        } else {
            $where = ['user_id' => $this->userInfo->id];
            $type = RecipeTypeTemplate::getList(['id', 'name'], $where);
            return $this->render('template/createInspectTemplate', [
                        'model' => $model,
                        'type' => $type,
            ]);
        }
    }

    /**
     * 
     * @return 检验医嘱模板修改
     */
    public function actionInspectTemplateUpdate($id) {
        $model = new MultiModel([
            'models' => [
                'inspectTemplate' => InspectTemplate::findInspectTemplateModel($id),
                'inspectTemplateInfo' => new InspectTemplateInfo()
            ]
        ]);
        $request = Yii::$app->request;
        if ($request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $inspectTemplateInfoModel = $model->getModel('inspectTemplateInfo');
                    if (isset($inspectTemplateInfoModel->clinic_inspect_id)) {
                        $upStatus = InspectTemplateInfo::updateInfo($model, $inspectTemplateInfoModel);
                        if (!$upStatus) {
                            $dbTrans->rollBack();
                            $this->result['errorCode'] = 1001;
                            $this->result['msg'] = '请选择检验医嘱';
                            return $this->result;
                        }
                    } else {
                        $this->result['errorCode'] = true;
                        $this->result['msg'] = '请选择检验医嘱';
                        return $this->result;
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
                } catch (Exception $e) {
                    Yii::error(json_encode($e->errorInfo, true), 'inspectTemplateCreate');
                    $dbTrans->rollBack();
                    $this->result['errorCode'] = 1002;
                    $this->result['msg'] = '操作失败';
                    return $this->result;
                }
            } else {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = $model->errors['inspectTemplate'][0][0] ? $model->errors['inspectTemplate'][0][0] : $model->errors['inspectTemplateInfo'][0][0];
                return $this->result;
            }
        } else {
            $where = ['user_id' => $this->userInfo->id];
            $type = RecipeTypeTemplate::getList(['id', 'name'], $where);
            $inspectList = InspectClinic::getInspectClinicList();
            $inspectTemplateInfoDataProvider = InspectTemplateInfo::findInspectTemplateInfoDataProvider($id);
            return $this->render('template/updateInspectTemplate', [
                        'model' => $model,
                        'type' => $type,
                        'inspectList' => $inspectList,
                        'inspectTemplateInfoDataProvider' => $inspectTemplateInfoDataProvider
            ]);
        }
    }

    public function actionInspectTemplateDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            InspectTemplate::findInspectTemplateModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            return $this->redirect(['index']);
        }
    }    
    protected function findRecipeTemplateInfoDataProvider($id){
        $query = new Query();
        $query->from(['a' => RecipeTemplateInfo::tableName()]);
        $query->select(['a.id', 'a.recipe_id', 'a.dose', 'a.dose_unit', 'a.used', 'a.frequency', 'a.day', 'a.num', 
            'a.description', 'a.type', 'a.skin_test_status', 'a.skin_test', 'a.curelist_id', 'c.dose_unit as recipe_dose_unit', 
            'c.specification','c.manufactor', 'd.price', 'c.name', 'c.medicine_description_id', 'c.type as recipeType','c.unit'
            ]);
        $query->leftJoin(['c' => RecipeList::tableName()], '{{a}}.recipe_id = {{c}}.id');
        $query->leftJoin(['d' => RecipelistClinic::tableName()], '{{a}}.clinic_recipe_id = {{d}}.id');
        $query->where(['a.recipe_template_id' => $id,'a.spot_id' => $this->spotId,'c.status' =>1]);
        $query->orderBy(['a.id' => SORT_ASC]);
        $result = $query->all();
        foreach ($result as $key => $value) {
            $doseUnit = explode(',', $value['recipe_dose_unit']);
            foreach ($doseUnit as $vals) {
                $all_dose_unit[$vals] = RecipeList::$getDoseUnit[$vals];
            }
            $result[$key]['recipe_dose_unit'] = $all_dose_unit;
        }
        return $result;
        
    }

    // ================================检验模板管理结束=========================
    
    
    //=================================治疗医嘱模版配置=========================
    /**
     * @return 治疗医嘱模板列表
     */
    public function actionCureTemplateIndex() {
        $searchModel = new CureTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        return $this->render('template/cureTemplate', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * @return 治疗医嘱模板列表
     */
    public function actionCureTemplateCreate() {
        $model = new MultiModel([
            'models' => [
                'cureTemplate' => new CureTemplate(),
                'cureTemplateInfo' => new CureTemplateInfo()
            ]
        ]);
        $request = Yii::$app->request;
        if ($request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $this->saveCureTemplate($model,1);
            return $this->result;
        } else {
            $where = ['user_id' => $this->userInfo->id];
            $type = RecipeTypeTemplate::getList(['id', 'name'], $where);
            return $this->render('template/createCureTemplate', [
                        'model' => $model,
                        'type' => $type,
            ]);
        }
    }
    
    /**
     * 
     * @return 检验医嘱模板修改
     */
    public function actionCureTemplateUpdate($id) {
        $model = new MultiModel([
            'models' => [
                'cureTemplate' => CureTemplate::findCureTemplateModel($id),
                'cureTemplateInfo' => new CureTemplateInfo()
            ]
        ]);
        $request = Yii::$app->request;
        if ($request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $this->saveCureTemplate($model,2);
            return $this->result;
        } else {
            $where = ['user_id' => $this->userInfo->id];
            $type = RecipeTypeTemplate::getList(['id', 'name'], $where);
            $cureTemplateInfoDataProvider = CureTemplateInfo::findCureTemplateInfoDataProvider($id);
            return $this->render('template/updateCureTemplate', [
                        'model' => $model,
                        'type' => $type,
                        'cureTemplateInfoDataProvider' => $cureTemplateInfoDataProvider,
            ]);
        }
    }
    
    /*
     * 保存治疗医嘱模板详情
     */
    public function saveCureTemplate($model,$type = 1) {
        $model->getModel('cureTemplate')->type = 2;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            try {
                $cureTemplateInfoModel = $model->getModel('cureTemplateInfo');
                if (isset($cureTemplateInfoModel->clinic_cure_id)) {
                    CureTemplateInfo::saveInfo($model, $cureTemplateInfoModel,$type);
                } else {
                    $dbTrans->rollBack();
                    $this->result['errorCode'] = 1001;
                    $this->result['msg'] = '请选择治疗医嘱';
                    return ;
                }
                $this->result['errorCode'] = 0;
                Yii::$app->getSession()->setFlash('success', '保存成功');
                $dbTrans->commit();
            } catch (Exception $e) {
                Yii::error(json_encode($e->errorInfo, true), 'cureTemplateCreate');
                $dbTrans->rollBack();
                $this->result['errorCode'] = 1002;
                $this->result['msg'] = '操作失败';
            }
        } else {
            $this->result['errorCode'] = 1001;
            $this->result['msg'] = $model->errors['cureTemplate'][0][0] ? $model->errors['cureTemplate'][0][0] : $model->errors['cureTemplateInfo'][0][0];
        }
        return ;
    }
    
    public function actionCureTemplateDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            CureTemplate::findCureTemplateModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            return $this->redirect(['index']);
        }
    }

    // ================================治疗模板管理结束=========================

    // ================================检查模板管理=========================
    /**
     * @return 检查模板列表
     */
    public function actionCheckTemplateIndex() {
        $searchModel = new CheckTemplateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        return $this->render('template/checkTemplate', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @return 新增检查模板
     */
    public function actionCheckTemplateCreate() {
        $model = new MultiModel([
            'models' => [
                'checkTemplate' => new CheckTemplate(),
                'checkTemplateInfo' => new CheckTemplateInfo()
            ]
        ]);
        $request = Yii::$app->request;
        if ($request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->getModel('checkTemplate')->type = 2;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $checkTemplateInfoModel = $model->getModel('checkTemplateInfo');
                    if (isset($checkTemplateInfoModel->clinic_check_id)) {
                        $saveStatus = CheckTemplateInfo::saveInfo($model, $checkTemplateInfoModel);
                        if (!$saveStatus) {
                            $dbTrans->rollBack();
                            $this->result['errorCode'] = 1001;
                            $this->result['msg'] = '请选择检查医嘱';
                            return $this->result;
                        }
                    } else {
                        $dbTrans->rollBack();
                        $this->result['errorCode'] = true;
                        $this->result['msg'] = '请选择检查医嘱';
                        return $this->result;
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
                } catch (Exception $e) {
                    Yii::error(json_encode($e->errorInfo, true), 'checkTemplateCreate');
                    $dbTrans->rollBack();
                    $this->result['errorCode'] = 1002;
                    $this->result['msg'] = '操作失败';
                    return $this->result;
                }
            } else {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = $model->errors['checkTemplate'][0][0] ? $model->errors['checkTemplate'][0][0] : $model->errors['checkTemplateInfo'][0][0];
                return $this->result;
            }
        } else {
            $where = ['user_id' => $this->userInfo->id];
            $type = RecipeTypeTemplate::getList(['id', 'name'], $where);
            return $this->render('template/createCheckTemplate', [
                'model' => $model,
                'type' => $type,
            ]);
        }
    }

    /**
     *
     * @return 检验医嘱模板修改
     */
    public function actionCheckTemplateUpdate($id) {
        $model = new MultiModel([
            'models' => [
                'checkTemplate' => CheckTemplate::findCheckTemplateModel($id),
                'checkTemplateInfo' => new CheckTemplateInfo()
            ]
        ]);
        $request = Yii::$app->request;
        if ($request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->validate()) {
                $dbTrans = Yii::$app->db->beginTransaction();
                try {
                    $checkTemplateInfoModel = $model->getModel('checkTemplateInfo');
                    if (isset($checkTemplateInfoModel->clinic_check_id)) {
                        $upStatus = CheckTemplateInfo::updateInfo($model, $checkTemplateInfoModel);
                        if (!$upStatus) {
                            $dbTrans->rollBack();
                            $this->result['errorCode'] = 1001;
                            $this->result['msg'] = '请选择检查医嘱';
                            return $this->result;
                        }
                    } else {
                        $this->result['errorCode'] = true;
                        $this->result['msg'] = '请选择检查医嘱';
                        return $this->result;
                    }
                    $this->result['errorCode'] = 0;
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    $dbTrans->commit();
                    return $this->result;
                } catch (Exception $e) {
                    Yii::error(json_encode($e->errorInfo, true), 'checkTemplateUpdate');
                    $dbTrans->rollBack();
                    $this->result['errorCode'] = 1002;
                    $this->result['msg'] = '操作失败';
                    return $this->result;
                }
            } else {
                $this->result['errorCode'] = 1001;
                $this->result['msg'] = $model->errors['checkTemplate'][0][0] ? $model->errors['checkTemplate'][0][0] : $model->errors['checkTemplateInfo'][0][0];
                return $this->result;
            }
        } else {
            $where = ['user_id' => $this->userInfo->id];
            $type = RecipeTypeTemplate::getList(['id', 'name'], $where);
            $checkTemplateInfoDataProvider = CheckTemplateInfo::findCheckTemplateInfoDataProvider($id);
            return $this->render('template/updateCheckTemplate', [
                'model' => $model,
                'type' => $type,
                'checkTemplateInfoDataProvider' => $checkTemplateInfoDataProvider
            ]);
        }
    }

    /**
     * @param $id 医嘱模板id
     * @return array
     * @desc 检查医嘱模板删除
     * @throws \app\modules\outpatient\models\NotFoundHttpException
     */
    public function actionCheckTemplateDelete($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            CheckTemplate::findCheckTemplateModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            return $this->redirect(['index']);
        }
    }

    // ================================检查模板管理结束=========================

}
