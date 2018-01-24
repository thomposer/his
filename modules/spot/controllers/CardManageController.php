<?php

namespace app\modules\spot\controllers;

use app\modules\spot\models\Spot;
use app\modules\spot_set\models\CardDiscountClinic;
use Yii;
use yii\web\Response;
use app\modules\spot\models\CardManage;
use app\modules\spot\models\search\CardManageSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\common\Common;
use app\modules\spot\models\search\CardRechargeCategorySearch;
use app\modules\spot\models\CardRechargeCategory;
use yii\helpers\Html;
use app\modules\spot\models\Tag;
use app\common\base\MultiModel;
use app\modules\spot\models\CardDiscount;
use yii\db\Exception;
use yii\db\Query;
use app\modules\spot\models\search\PackageCardSearch;
use app\modules\spot\models\search\PackageCardServiceSearch;
use app\modules\spot\models\PackageCard;
use app\modules\spot\models\PackageCardService;
use app\modules\spot\models\PackageServiceUnion;
use yii\helpers\Url;
use app\specialModules\recharge\models\MembershipPackageCard;

// 卡失效时间时长 单位：天
define("EXPIRED_TIME", 180, true);

/**
 * CardManageController implements the CRUD actions for CardManage model.
 */
class CardManageController extends BaseController
{

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all CardManage models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CardManageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CardManage model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new CardManage model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new CardManage();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing CardManage model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if (Yii::$app->request->isPost) {
            if ($model->f_status == 0) {
                $model->f_activate_time = time();
                $model->f_invalid_time = $model->f_activate_time + EXPIRED_TIME * 24 * 3600;
                $model->f_status = 1;
            } else if ($model->f_status == 1) {
                $model->f_status = 2;
            } else if ($model->f_status == 2) {
                $model->f_status = 1;
            }
            if ($model->f_status == 2 || $model->f_status == 1) {//停用   同步到APP
                $url = Yii::$app->params['hisApiHost'] . Yii::getAlias('@cardCenterSyncApp');
                if ($model->f_status == 2) {
                    $state = 2;
                } elseif ($model->f_status == 1) {
                    $state = 0;
                }
                $cardInfo = Common::curlPost($url, ['card_number' => $model->f_card_id, 'state' => $state]);
            }
            $res = $model->save();
//            var_dump($res);exit;
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceRedirect' => \yii\helpers\Url::to(['index'])];
        } else {
            $model->f_status = CardManage::$getStatus[$model->f_status];
            $model->f_is_issue = CardManage::$getIssue[$model->f_is_issue];
            $model->f_create_time != 0 ? $model->f_create_time = date('Y-m-d', $model->f_create_time) : $model->f_create_time = "";
            $model->f_effective_time != 0 ? $model->f_effective_time = date('Y-m-d', $model->f_effective_time) : $model->f_effective_time = "";
            $model->f_activate_time != 0 ? $model->f_activate_time = date('Y-m-d', $model->f_activate_time) : $model->f_activate_time = "";
            $model->f_invalid_time != 0 ? $model->f_invalid_time = date('Y-m-d', $model->f_invalid_time) : $model->f_invalid_time = "";
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Delete an existing CardManage model.
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
     * Finds the CardManage model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return CardManage the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = CardManage::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /*     * ***
     * 
     * 
     * 充值卡 卡组 配置 
     * 
     * *****
     */

    public function actionGroupIndex() {
        $searchModel = new CardRechargeCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('/card-group/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new CardRechargeCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionGroupCreate() {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new CardRechargeCategory();
            $model->scenario = 'group';
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'forceClose' => true
                ];
            } else {
                $ret = [
                    'title' => "新建卡组",
                    'content' => $this->renderAjax('/card-group/create', [
                        'model' => $model,
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
     * Updates an existing CardRechargeCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionGroupUpdate($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findCardCategoryModel($id);
            $model->scenario = 'group';
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'forceClose' => true
                ];
            } else {
                $ret = [
                    'title' => "修改卡组",
                    'content' => $this->renderAjax('/card-group/update', [
                        'model' => $model,
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
     * Displays a single CardRechargeCategory model.
     * @param string $id
     * @return mixed
     */
    public function actionGroupView($id) {
        return $this->render('/card-group/view', [
                    'model' => $this->findCardCategoryModel($id),
        ]);
    }

    /**
     * 
     * @return type 子项目
     * @throws NotFoundHttpException
     */
    public function actionSubclass() {
        if (isset($_POST['expandRowKey'])) {
            $dataProvider = CardRechargeCategorySearch::findSubDataProvider($_POST['expandRowKey']);
            return $this->renderPartial('/card-group/_subclass', ['dataProvider' => $dataProvider]);
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /*     * ***
     * 
     * 
     * 充值卡 卡种 配置 
     * 
     * *****
     */

    /**
     * Creates a new CardRechargeCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCategoryCreate() {
        $request = Yii::$app->request;
        if ($request->isAjax) {

            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = new CardRechargeCategory();
            $model->scenario = 'category';
            $cardCategory = CardRechargeCategory::getCategory();
            if ($model->load($request->post()) && $model->validate()) {
                $dbTrans = $model->getDb()->beginTransaction();
                try {
                    $tagId = $request->post()['tag_id'];
                    $model->save();
                    if (count($tagId) > 0) {
                        if (count($tagId) != count(array_unique($tagId))) {
                            $this->result['errorCode'] = 1001;
                            $this->result['msg'] = '标签不能重复';
                            $dbTrans->rollBack();
                            return $this->result;
                        }
                        foreach ($tagId as $key => $v) {
                            if ($v != '') {
                                //若折扣输入有误，则弹窗提示
                                $rows[] = [$this->parentSpotId, $model->f_physical_id, $v, time(), time()];
                            }
                        }
                        $model->getDb()->createCommand()->batchInsert(CardDiscount::tableName(), ['spot_id', 'recharge_category_id', 'tag_id', 'create_time', 'update_time'], $rows)->execute();
                    }
                    $dbTrans->commit();
                    $this->result['msg'] = '保存成功';
                    return $this->result;
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    throw $e;
                }
            } else {
                if (empty($model->errors)) {
                    $model->f_auto_upgrade = 2;
                } else {
                    $this->result['errorCode'] = '1003';
                    $this->result['msg'] = $model->errors['f_upgrade_amount'];
                    return $this->result;
                }
                $tagList = Tag::getTagList(['id', 'name'], ['status' => 1,'type'=>1]);
                $ret = [
                    'title' => "新建卡种",
                    'content' => $this->renderAjax('/card-category/create', [
                        'model' => $model,
                        'cardCategory' => $cardCategory,
                        'tagList' => $tagList,
                        'cardDiscountList' => [['tag_id' => '']]
                    ]),
//                     'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
//                     Html::button('确认', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
                return $ret;
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Updates an existing CardRechargeCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionCategoryUpdate($id) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findCardCategoryModel($id);
            $model->scenario = 'category';
            $cardCategory = CardRechargeCategory::getCategory();
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                $dbTrans = $model->getDb()->beginTransaction();
                try {
                    $tagId = $request->post()['tag_id'];
                    $delete = $request->post()['deleted'];
                    $newRecord = $request->post()['newRecord'];
                    $change = $request->post()['change'];
                    $rows = [];
                    $deleteRows = [];
                    $model->save();
                    if (!empty($tagId)) {
                        $tagId = array_values(array_filter($tagId));
                        foreach ($tagId as $key => $v) {
                            if ($v) {
                                //若折扣输入有误，则弹窗提示
                                /*    if(!preg_match('/^(((\d|[1-9]\d)(\.[0-9]{1,2})?)|(100(\.0{1,2})?))?$/', $tagDiscount[$key])){
                                  $dbTrans->rollBack();
                                  $this->result['errorCode'] = 1002;
                                  $this->result['msg'] = '只能精确到小数点后两位,且在0-100间';
                                  return $this->result;
                                  }else{
                                  $rows[] = [$this->parentSpotId,$model->f_physical_id,$v,$tagDiscount[$key] !== ''?$tagDiscount[$key]:100,time(),time()];
                                  } */

                                if ($newRecord[$key] == 1) {//新增
                                    $uniqueTag[] = $v;
                                    $rows[] = [$this->parentSpotId, $model->f_physical_id, $v, time(), time()];
                                }
                                if ($newRecord[$key] == 2 && $delete[$key] == 1 && $change[$key] <= 0) {//删除的数据
                                    $deleteRows[] = $change[$key] > 0 ? $change[$key] : $v;
                                }
                                if ($newRecord[$key] == 2 && $delete[$key] == 2 && $change[$key] > 0) {
                                    $rows[] = [$this->parentSpotId, $model->f_physical_id, $v, time(), time()];
                                    $deleteRows[] = $change[$key];
                                }
                                if ($newRecord[$key] == 2 && $delete[$key] == 2) {//原数据
                                    $uniqueTag[] = $v;
                                }
                            }
                        }
                        if (!empty($uniqueTag)) {
                            if (count($uniqueTag) != count(array_unique($uniqueTag))) {
                                $this->result['errorCode'] = 1001;
                                $this->result['msg'] = '标签不能重复';
                                $dbTrans->rollBack();
                                return $this->result;
                            }
                        }
//                        print_r($rows);
//                        print_r($deleteRows);exit;
                        !empty($rows) && $model->getDb()->createCommand()->batchInsert(CardDiscount::tableName(), ['spot_id', 'recharge_category_id', 'tag_id', 'create_time', 'update_time'], $rows)->execute();
                        if (!empty($deleteRows)) {
                            // 删除t_card_discount机构表中的当前机构下 当前卡种 当前标签的数据
                            CardDiscount::deleteAll(['recharge_category_id' => $model->f_physical_id, 'spot_id' => $this->parentSpotId, 'tag_id' => $deleteRows]);
                            // 删除所有  t_card_discount_clinic表中的当前机构下 当前卡种 当前标签的数据
                            CardDiscountClinic::deleteAll(['recharge_category_id' => $model->f_physical_id, 'parent_spot_id' => $this->parentSpotId, 'tag_id' => $deleteRows]);
                        }
                    }
                    $dbTrans->commit();
                    $this->result['msg'] = '保存成功';
                    return $this->result;
                } catch (Exception $e) {
                    $dbTrans->rollBack();
                    throw $e;
                }
            } else {
                if (!empty($model->errors)) {
                    $this->result['errorCode'] = '1003';
                    $this->result['msg'] = $model->errors['f_upgrade_amount'];
                    return $this->result;
                }
                $tagList = Tag::getTagList(['id', 'name'], ['status' => 1,'type'=>1]);
                $cardDiscountList = CardDiscount::find()->select(['tag_id'])->where(['recharge_category_id' => $model->f_physical_id, 'spot_id' => $this->parentSpotId])->andWhere(['>', 'tag_id', 0])->asArray()->all();
                $ret = [
                    'title' => "修改卡种",
                    'content' => $this->renderAjax('/card-category/update', [
                        'model' => $model,
                        'cardCategory' => $cardCategory,
                        'tagList' => $tagList,
                        'cardDiscountList' => !empty($cardDiscountList) ? $cardDiscountList : [['tag_id' => '']]
                    ]),
//                     'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
//                     Html::button('确认', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
                return $ret;
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Displays a single CardRechargeCategory model.
     * @param string $id
     * @return mixed
     */
    public function actionCategoryView($id) {
        $cardCategory = CardRechargeCategory::getCategory();
        $data = CardRechargeCategory::getCatServiceListById($id);


//         $query = new Query();
//         $query->from(['a' => Yii::$app->getDb('cardCenter').CardDiscount::tableName()]);
//         $query->select(['a.tag_id','a.discount','b.name']);
//         $query->leftJoin(['b' => Tag::tableName()],'{{a}}.tag_id = {{b}}.id');
//         $query->where(['a.spot_id' => $this->parentSpotId,'a.recharge_category_id' => $id]);
//         $cardDiscountList = $query->all();
//         var_dump($cardDiscountList);
        return $this->render('/card-category/view', [
                    'model' => $this->findCardCategoryModel($id),
                    'cardCategory' => $cardCategory,
                    'data' => $data,
        ]);
    }

    /**
     * 
     * @param type $id 卡组/卡种 ID
     * @return 发行/停止发行卡
     */
    public function actionCategoryOperation($id) {
        $request = Yii::$app->request;
         Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isPost) {
            $model = $this->findCardCategoryModel($id);
            if ($model->f_state == 1 || $model->f_state == 3) {//
                $model->f_state = 2;
            } else {
                $model->f_state = 3;
            }
            $model->save();
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        } else {
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }
    }

    public function findCardCategoryModel($id) {
        if (($model = CardRechargeCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
    
    /*
     * 套餐卡配置
     */
    public function actionPackageCardIndex() {
        $searchModel = new PackageCardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('/package-card/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }
    
    
    /*
     * 新增套餐卡
     */
    public function actionPackageCardCreate() {
        $model = new PackageCard();
        $model->scenario = 'create';
        $packageServiceUnionModel = new PackageServiceUnion();

        $request = Yii::$app->request;
        if ($request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate() && ($ret = $packageServiceUnionModel->load(Yii::$app->request->post())) && $packageServiceUnionModel->validate()) {
                if($this->savePackageCardData($model, $packageServiceUnionModel)){
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(['package-card-index']);
                }else{
                    Yii::$app->getSession()->setFlash('success', '保存失败');
                    return $this->redirect(['package-card-index']);
                }
            } else {
                $this->result['errorCode'] = 1009;
                if($model->errors){
                    $this->result['msg'] = array_values($model->errors)[0][0];
                }else if(!$ret){
                    $this->result['msg'] = '服务信息不能为空';
                }else{
                    $this->result['msg'] = array_values($packageServiceUnionModel->errors)[0][0];
                }
                return $this->result;
            }
        } else {
            $packageCardServiceList = array_column(PackageCardService::getServiceList(['status' => 1]), 'name', 'id');
            return $this->render('/package-card/create', [
                        'model' => $model,
                        'packageCardServiceList' => $packageCardServiceList,
            ]);
        }
    }

    /*
     * 修改套餐卡
     */
    public function actionPackageCardUpdate($id) {
        $model = $this->findPackageCardModel($id);
        $packageServiceUnionModel = new PackageServiceUnion();
        $request = Yii::$app->request;
        if ($request->isPost) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate() && ($ret = $packageServiceUnionModel->load(Yii::$app->request->post())) && $packageServiceUnionModel->validate()) {
                if($this->savePackageCardData($model, $packageServiceUnionModel)){
                    Yii::$app->getSession()->setFlash('success', '保存成功');
                    return $this->redirect(['package-card-index']);
                }else{
                    Yii::$app->getSession()->setFlash('success', '保存失败');
                    return $this->redirect(['package-card-index']);
                }
            } else {
                $this->result['errorCode'] = 1009;
                if($model->errors){
                    $this->result['msg'] = array_values($model->errors)[0][0];
                }else if(!$ret){
                    $this->result['msg'] = '服务信息不能为空';
                }else{
                    $this->result['msg'] = array_values($packageServiceUnionModel->errors)[0][0];
                }
                return $this->result;
            }
        } else {
            $packageServiceUnionList = PackageServiceUnion::getUnionList($id);
            $packageCardServiceList = array_column(PackageCardService::getServiceList(['status' => 1]), 'name', 'id');
            return $this->render('/package-card/update', [
                        'model' => $model,
                        'packageCardServiceList' => $packageCardServiceList,
                        'packageServiceUnionList' => $packageServiceUnionList,
            ]);
        }
    }
    /*
     * 保存卡信息
     */
    protected function savePackageCardData($model,$packageServiceUnionModel) {
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            $db = Yii::$app->db;
            $model->save();
            if(MembershipPackageCard::getBuyCount($model->id) == 0){//没有用户购买时才可修改
                $db->createCommand()->delete(PackageServiceUnion::tableName(), ['spot_id' => $this->parentSpotId, 'package_card_id' => $model->id])->execute();//更新时起作用
                foreach ($packageServiceUnionModel->package_card_service_id as $key => $value) {
                    $rows[] = array(
                        'spot_id' => $this->parentSpotId,
                        'package_card_id' => $model->id,
                        'package_card_service_id' => $packageServiceUnionModel->package_card_service_id[$key],
                        'time' => $packageServiceUnionModel->time[$key],
                        'create_time' => time(),
                        'update_time' => time(),
                    );
                }
                $db->createCommand()->batchInsert(PackageServiceUnion::tableName(), ['spot_id', 'package_card_id', 'package_card_service_id', 'time', 'create_time', 'update_time'], $rows)->execute();
            }
            $dbTrans->commit();
            return true;
        } catch (Exception $e) {
            $dbTrans->rollBack();
            return false;

        }
    }

    /**
     * 修改套餐卡状态
     */
    public function actionPackageCardUpdateStatus($id)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $model = $this->findPackageCardModel($id);
            $model->status = $model->status == 1 ? 2 : 1;
            $model->save();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            return $this->redirect(['package-card-index']);
        }
    }

    public function findPackageCardModel($id) {
        if (($model = PackageCard::find()->where(['id' => $id, 'spot_id' => $this->parentSpotId])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
    
     /*
     * 套餐卡服务类型配置
     */
    public function actionPackageCardServiceIndex() {
        $searchModel = new PackageCardServiceSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('/package-card-service/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }
    
    /*
     * 新增套餐卡服务类型
     */
    public function actionPackageCardServiceCreate()
    {
        $request = Yii::$app->request;
        $model = new PackageCardService();  

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "新增套餐卡服务类型",
                    'content'=>$this->renderAjax('/package-card-service/create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                               Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'forceClose' => true
                ];         
            }else{
                return [
                    'title'=> "新增套餐卡服务类型",
                    'content'=>$this->renderAjax('/package-card-service/create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                               Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
        
                ];         
            }
        }else{
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
       
    }
    
    
    /**
     * 修改套餐卡服务类型
     */
    public function actionPackageCardServiceUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findPackageCardServiceModel($id);       

        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "修改套餐卡服务类型",
                    'content'=>$this->renderAjax('/package-card-service/update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                               Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'forceClose' => true
                ];    
            }else{
                return [
                    'title'=> "修改套餐卡服务类型",
                    'content'=>$this->renderAjax('/package-card-service/update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('取消',['class'=>'btn btn-cancel btn-form','data-dismiss'=>"modal"]).
                               Html::button('保存',['class'=>'btn btn-default btn-form','type'=>"submit"])
                ];         
            }
        }else{
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * 修改套餐卡服务类型状态
     */
    public function actionPackageCardServiceUpdateStatus($id)
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            $model = $this->findPackageCardServiceModel($id);
            $model->status = $model->status == 1 ? 2 : 1;
            $model->save();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            return $this->redirect(['package-card-index']);
        }
    }
    
    public function findPackageCardServiceModel($id) {
        if (($model = PackageCardService::find()->where(['id' => $id, 'spot_id' => $this->parentSpotId])->one()) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
