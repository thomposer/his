<?php

namespace app\modules\card\controllers;

use Yii;
use yii\web\Response;
use app\modules\card\models\UserCard;
use app\modules\card\models\serarch\UserCardSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\common\Common;
use yii\helpers\Html;
use yii\helpers\Url;
use app\modules\card\models\ServiceConfig;
use app\modules\card\models\CardServiceLeft;

/**
 * IndexController implements the CRUD actions for UserCard model.
 */
class IndexController extends BaseController
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
     * Lists all UserCard models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new UserCardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        $card_physical_id = [];
        $cardInfo = [];
        if ($dataProvider->getModels()) {
//            $card_physical_id = $dataProvider->query->asArray()->all();
//            $card_physical_id = array_column($all, 'card_id');
            foreach ($dataProvider->getModels() as $model) {
                $card_physical_id[] = $model->getAttributes();
            }
        }
        if ($card_physical_id) {
            try {
                $url = Yii::$app->request->getHostInfo() . Url::to(['@cardCenterCardInfoBySn']);
                $cardInfo = Common::curlPost($url, ['f_card_id' => $card_physical_id]);
                $cardInfo = $cardInfo ? json_decode($cardInfo, true) : '';
            } catch (Exception $exc) {
                $cardInfo = [];
            }
        }
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'cardInfo' => $cardInfo,
        ]);
    }

    /**
     * 
     * @return 验证会员卡
     */
    public function actionCheck() {
        $model = new UserCard();
        $model->scenario = 'check';
        if (Yii::$app->request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                $record = UserCard::checkCard($model->checkType, $model->checkNum);
                if ($record) {//有数据
                    return ['forceClose' => true, 'forceRedirect' => Url::to(['create', 'record' => $record])];
//                    $this->redirect(Url::to(['create', 'record' => $record]));
                } else {
                    Yii::$app->getSession()->setFlash('error', '验证失败');
//                    return $this->redirect(['index']);
                    return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
                }
            } else {
                return [
                    'title' => "验证",
                    'content' => $this->renderAjax('check', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"])  .
                        Html::button('验证', ['class' => 'btn btn-default btn-form card-check-btn', 'type' => "submit"])
                ];
            }
        } elseif (($id = Yii::$app->request->get('id'))) {
            $userCard = $this->findModel($id);
            $record = UserCard::checkCard(1, $userCard->card_id);
            if ($record) {//有数据
                $this->redirect(Url::to(['create', 'record' => $record]));
            } else {
                Yii::$app->getSession()->setFlash('error', '验证失败');
                return $this->redirect(['index']);
            }
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /**
     * Creates a new UserCard model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new UserCard();
        $model->scenario = 'create';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $dbTrans = Yii::$app->db->beginTransaction();
            $text='保存成功';
            try {
                if ($model->id) {
                    $model->updateAll([
                        'user_name' => $model->user_name,
                        'phone' => $model->phone,
                            ], ['id' => $model->id]);
                } else {
                    //调用卡中心  激活接口激活卡   现在改为直接激活服务
                    UserCard::activateCard($model->card_id,$model->card_type);
                    if (!$model->card_physical_id) {
                        $model->card_type = 2;
                    }
                    $model->save();
                    $text='激活成功';
                }
                $cardRecord = Yii::$app->request->get('record');
                $service_left = Yii::$app->request->post('UserCard')['service_left'];
                if ($service_left != '' && ($service_id = Yii::$app->request->post('service_id'))) {//修改剩余次数
                    $serviceRecord = CardServiceLeft::find()->where(['card_id' => $cardRecord['f_card_id'], 'service_id' => $service_id])->one();
                    if ($serviceRecord) {//修改
                        $serviceRecord->service_left = $service_left;
                    } else {
                        $serviceRecord = new CardServiceLeft();
                        $serviceRecord->service_left = $service_left;
                        $serviceRecord->card_physical_id = isset($cardRecord['f_physical_id']) ? $cardRecord['f_physical_id'] : 0;
                        $serviceRecord->service_id = $service_id;
                        $serviceRecord->card_id = $cardRecord['f_card_id'];
                        $serviceRecord->activate_time = time();
                        $serviceRecord->invalid_time = time() + 365 * 24 * 60 * 60;
                    }
                    $serviceRecord->save();
                }
                $dbTrans->commit();
            } catch (Exception $e) {
                $dbTrans->rollBack();
            }
            Yii::$app->getSession()->setFlash('success',$text);
            return $this->redirect(['index']);
        } else {
            $record = Yii::$app->request->get('record');
            if ($record['dataFrom'] == 1) {//本地已经有数据  更新
                $model->id = $record['id'];
                $model->user_name = $record['user_name'];
                $model->phone = $record['phone'];
            }
            $model->card_id = $record['f_card_id'];
            $model->card_physical_id = isset($record['f_physical_id']) ? $record['f_physical_id'] : 0;
            $model->card_type_code = $record['f_card_type_code'];
            $model->parent_spot_id = $this->parentSpotId;
            $model->f_effective_time = $record['f_effective_time'];
            $model->card_type = $record['card_type'];
            //服务信息
            $service = ServiceConfig::find()->where(['card_type' => $record['f_card_type_code']])->asArray()->one();
            //剩余次数
            $left = [];
            if ($service) {
                $left = CardServiceLeft::find()->where(['card_id' => $record['f_card_id'], 'service_id' => $service['id']])->asArray()->one();
            }
            return $this->render('create', [
                        'model' => $model,
                        'record' => $record,
                        'service' => $service,
                        'left' => $left,
            ]);
        }
    }

    /**
     * Updates an existing UserCard model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Delete an existing UserCard model.
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
     * Finds the UserCard model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return UserCard the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = UserCard::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

}
