<?php

namespace app\modules\spot_set\controllers;

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
use app\modules\spot_set\models\CardDiscountClinic;

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

    /*
     * 
     * 
     * ***************充值卡 卡组 配置 *******************
     * 
     * 
     */

    public function actionGroupIndex() {
        $searchModel = new CardRechargeCategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);

        return $this->render('groupIndex', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CardRechargeCategory model.
     * @param string $id
     * @return mixed
     */
    public function actionGroupView($id) {
        return $this->render('groupView', [
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
            return $this->renderPartial('_groupSubclass', ['dataProvider' => $dataProvider]);
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
            $cardCategory = CardRechargeCategory::getCategory();
            if (Yii::$app->request->post()) {
                $dbTrans = $model->getDb()->beginTransaction();
                try {
                    $tagId = $request->post()['CardDiscountClinic']['tag_id'];
                    $tagDiscount = $request->post()['CardDiscountClinic']['discount'];
                    $cardDiscountId = $request->post()['CardDiscountClinic']['card_discount_id'];
                    if (!empty($tagId)) {
                        foreach ($tagId as $key => $v) {
                            if ($cardDiscountId[$key]) {
                                //若折扣输入有误，则弹窗提示
                                if ($tagDiscount[$key] == '' || !preg_match('/^(((\d|[1-9]\d)(\.[0-9]{1,2})?)|(100(\.0{1,2})?))?$/', $tagDiscount[$key])) {
                                    $dbTrans->rollBack();
                                    $this->result['errorCode'] = 1002;
                                    $this->result['msg'] = '必填且精确到小数点后两位,在0-100间';
                                    return $this->result;
                                } else {
                                    $rows[] = [$cardDiscountId[$key], $this->parentSpotId, $this->spotId, $model->f_physical_id, $v, $tagDiscount[$key] !== '' ? $tagDiscount[$key] : 100, time(), time()];
                                }
                            }
                        }
                        CardDiscountClinic::deleteAll(['recharge_category_id' => $model->f_physical_id, 'spot_id' => $this->spotId]);
                        $model->getDb()->createCommand()->batchInsert(CardDiscountClinic::tableName(), ['card_discount_id', 'parent_spot_id', 'spot_id', 'recharge_category_id', 'tag_id', 'discount', 'create_time', 'update_time'], $rows)->execute();
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
                $cardDiscountList = CardDiscountClinic::cardDiscountClinic($model->f_physical_id);
                $ret = [
                    'title' => "修改卡种",
                    'content' => $this->renderAjax('categoryUpdate', [
                        'model' => $model,
                        'cardDiscountList' => $cardDiscountList,
                        'cardCategory' => $cardCategory
                    ]),
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
//         $query = new Query();
//         $query->from(['a' => Yii::$app->getDb('cardCenter').CardDiscount::tableName()]);
//         $query->select(['a.tag_id','a.discount','b.name']);
//         $query->leftJoin(['b' => Tag::tableName()],'{{a}}.tag_id = {{b}}.id');
//         $query->where(['a.spot_id' => $this->parentSpotId,'a.recharge_category_id' => $id]);
//         $cardDiscountList = $query->all();
//         var_dump($cardDiscountList);  
        $cardDiscountList = CardDiscountClinic::cardDiscountListClinic($id);
        return $this->render('categoryView', [
                    'model' => $this->findCardCategoryModel($id),
                    'cardCategory' => $cardCategory,
                    'cardDiscountList' => $cardDiscountList
        ]);
    }

    public function findCardCategoryModel($id) {
        if (($model = CardRechargeCategory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

}
