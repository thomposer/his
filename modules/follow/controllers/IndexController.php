<?php

namespace app\modules\follow\controllers;

use Yii;
use yii\web\Response;
use app\modules\follow\models\Follow;
use app\modules\follow\models\search\FollowSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\patient\models\Patient;
use app\modules\patient\models\PatientRecord;
use app\modules\triage\models\TriageInfo;
use yii\helpers\Html;
use app\modules\user\models\User;
use yii\db\Query;
use app\modules\user\models\UserSpot;
use yii\data\ArrayDataProvider;
use app\modules\follow\models\Message;
use app\common\Common;
use yii\helpers\Url;

/**
 * IndexController implements the CRUD actions for Follow model.
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
     * Lists all Follow models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new FollowSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $this->pageSize);
        $countData = $searchModel->getFollowStateCount($this->spotId);
        //获取诊所所有状态正常的员工
        $userInfo = User::getSpotUser();
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'userInfo' => $userInfo,
                    'countData' => $countData,
        ]);
    }

    /**
     * Displays a single Follow model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);
        $model->scenario = 'createFollow';
        $recordId = $model->record_id;
        $patientId = $model->patient_id;
        if (!$recordId || !$patientId) {
            throw new NotFoundHttpException('你所请求的页面不存在');
            return;
        }
        $patientModel = $this->findPatientInfo($patientId);
        $triageInfo = TriageInfo::getTriageInfo($recordId);
        if (!$patientModel || !$triageInfo) {
            throw new NotFoundHttpException('你所请求的页面不存在');
            return;
        }
        $triageInfo['spotName'] = $this->spotName;
        $model->planCreatorName = User::getUserInfo($model->plan_creator, ['username'])['username'];
        $model->followExecutorName = User::getUserInfo($model->follow_executor, ['username'])['username'];
        $model->cancelUserName = User::getUserInfo($model->cancel_user, ['username'])['username'];
        $model->complete_time = date('Y-m-d', $model->complete_time);
        $model->create_time = date('Y-m-d', $model->create_time);
        $model->cancel_time = date('Y-m-d', $model->cancel_time);
        $followFile = Follow::findFollowFile($id);
        return $this->render('view', [
                    'model' => $model,
                    'patientModel' => $patientModel,
                    'triageInfo' => $triageInfo,
                    'followFile' => $followFile
        ]);
    }

    /**
     * Creates a new Follow model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Follow();
        $model->scenario = 'createFollow';
        $recordId = Yii::$app->request->get('recordId');
        $patientId = Yii::$app->request->get('patientId');
        if (!$recordId || !$patientId) {
            throw new NotFoundHttpException('你所请求的页面不存在');
            return;
        }
        $patientModel = $this->findPatientInfo($patientId);
        $triageInfo = TriageInfo::getTriageInfo($recordId);
        if (!$patientModel || !$triageInfo) {
            throw new NotFoundHttpException('你所请求的页面不存在');
            return;
        }
        $triageInfo['spotName'] = $this->spotName;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->record_id = $recordId;
            $model->patient_id = $patientId;
            $recordCount = Follow::recordCount($recordId);
            if (!empty($recordCount)) {
                Yii::$app->getSession()->setFlash('error', '已有随访记录');
            } else {
                $model->save();
                Yii::$app->getSession()->setFlash('success', '保存成功');
            }
            return $this->redirect(['index']);
        } else {
            $model->create_time = date('Y-m-d');
            return $this->render('create', [
                        'model' => $model,
                        'patientModel' => $patientModel,
                        'triageInfo' => $triageInfo
            ]);
        }
    }

    /**
     * Updates an existing Follow model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $model->scenario = 'createFollow';
        $recordId = $model->record_id;
        $patientId = $model->patient_id;
        if (!$recordId || !$patientId || $model->follow_state == 4) {
            throw new NotFoundHttpException('你所请求的页面不存在');
            return;
        }
        $patientModel = $this->findPatientInfo($patientId);
        $triageInfo = TriageInfo::getTriageInfo($recordId);
        $model->complete_time = date('Y-m-d', $model->complete_time);
        if (!$patientModel || !$triageInfo) {
            throw new NotFoundHttpException('你所请求的页面不存在');
            return;
        }
        $triageInfo['spotName'] = $this->spotName;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            $model->create_time = date('Y-m-d', $model->create_time);
            return $this->render('update', [
                        'model' => $model,
                        'patientModel' => $patientModel,
                        'triageInfo' => $triageInfo
            ]);
        }
    }

    public function actionExecute($id) {
        $model = $this->findModel($id);
        $model->scenario = 'executeFollow';
        $recordId = $model->record_id;
        $patientId = $model->patient_id;
        if (!$recordId || !$patientId || $model->follow_state == 4) {
            throw new NotFoundHttpException('你所请求的页面不存在');
            return;
        }
        $patientModel = $this->findPatientInfo($patientId);
        $triageInfo = TriageInfo::getTriageInfo($recordId);
        $model->complete_time = date('Y-m-d', $model->complete_time);
        if (!$patientModel || !$triageInfo) {
            throw new NotFoundHttpException('你所请求的页面不存在');
            return;
        }
        $triageInfo['spotName'] = $this->spotName;
        $followFile = Follow::findFollowFile($id);
        if ($model->plan_creator) {
            $model->planCreatorName = User::getUserInfo($model->plan_creator, ['username'])['username'];
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->getSession()->setFlash('success', '保存成功');
            return $this->redirect(['index']);
        } else {
            $model->create_time = date('Y-m-d', $model->create_time);
            return $this->render('execute', [
                        'model' => $model,
                        'patientModel' => $patientModel,
                        'triageInfo' => $triageInfo,
                        'followFile' => $followFile
            ]);
        }
    }

    public function actionCancel($id,$orgin = null) {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = $this->findModel($id);
            $model->scenario = 'cancelFollow';
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if (in_array($model->follow_state, [2, 3])) {
                    return [
                        'forceClose' => true,
                        'forceMessage' => "操作失败",
                        'forceReload' => '#crud-datatable-pjax'
                    ];
                } else {
                    $model->follow_state = 4;
                    $model->save();
                    if($orgin == 1){
                        return [    
                            'forceClose' => true,
                            'forceMessage' => '操作成功',
                            'forceReloadPage' => 'true',
                        ];
                    }
                    return [
                        'forceClose' => true,
                        'forceMessage' => "操作成功",
                        'forceReload' => '#crud-datatable-pjax'
                    ];
                }
            } else {
                return [
                    'title' => "取消随访",
                    'content' => $this->renderAjax('cancel', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('取消', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('保存', ['class' => 'btn btn-default btn-form card-check-btn', 'type' => "submit"])
                ];
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * @desc 对话消息 
     */
    public function actionDialogMessage($id) {
        $patientInfo = Follow::patientInfo($id);
        if (!$patientInfo) {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
        $bindInfo = [];
        $data = [];
        if ($patientInfo['mommyknows_account']) {
            //绑定消息
            $bindInfoRes = json_decode(Common::curlPost(Yii::$app->params['hisApiHost'] . Yii::getAlias('@hisapiFollowBindInfo'), ['telphone' => $patientInfo['mommyknows_account']]), true);
            $bindInfo = $bindInfoRes['err_code'] == 0 ? $bindInfoRes['userInfo'] : [];
            //消息列表
            if ($bindInfo) {
                $messageRes = json_decode(Common::curlPost(Yii::$app->params['hisApiHost'] . Yii::getAlias('@hisapiFollowMessage'), ['clientId' => $bindInfo['clientId']]), true);
                if ($messageRes['err_code'] == 0 && $messageRes['message']) {
//                    print_r($messageRes['message']);
//                    exit;
                    foreach ($messageRes['message'] as $val) {
                        $res = [
                            'sender' => '',
                            'message' => '',
                            'sendTime' => $val['sendTime'],
                            'attachment' => '',
                            'sendType' => 1,
                            'from' => $val['from'],
                            'send' => $val['send'],
                            'userName' => $val['userName'],
                        ];
                        if ($val['from'] === 'healthadmin') {
                            $res['sender'] = '医信健康助手';
                        } else {
                            $res['sender'] = $val['userName'];
                        }
                        $content = json_decode($val['rawData'], true);
                        if ($content['payload']['meta']['payload']['contents'][0]['contenttype'] == 'TXT') {
                            $res['message'] = $content['payload']['meta']['payload']['contents'][0]['text'];
                            $res['sendType'] = 1;
                        } else if ($content['payload']['meta']['payload']['contents'][0]['contenttype'] == 'IMAGE') {
                            $res['attachment'] = $content['payload']['meta']['payload']['contents'][0]['remotepath'];
                            $res['sendType'] = 2;
                        } else {
                            $res['message'] = '不识别的消息类型';
                            $res['sendType'] = 1;
                        }
                        $data[] = $res;
                    }
                }
            }
        }
//        $bindInfo = ['telphone' => 18576617068, 'nickName' => '太古妈妈'];
//        print_r($data);exit;
        $dataProvider = new ArrayDataProvider([
            'allModels' => $data,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);
        return $this->render('dialogMessage', [
                    'dataProvider' => $dataProvider,
                    'bindInfo' => $bindInfo
        ]);
    }

    /**
     * @desc 发送消息弹窗
     */
    public function actionSendMessage() {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii ::$app->response->format = Response::FORMAT_JSON;
            $model = new Message();
            $telphone = Yii::$app->request->get('telphone');
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                //发送消息
                $postParam = [
                    'visitorId' => 'admin',
                    'phone' => $telphone,
                ];
                if ($model->message) {//普通文字
                    $postParam['type'] = 1;
                    $postParam['content'] = $model->message;
                    $chatRes = json_decode(Common::curlPost(Yii::$app->params['hisApiHost'] . Yii::getAlias('@hisapiFollowChat'), $postParam), true);
                }
                if ($model->attachment) {//附件消息
                    $postParam['type'] = 2;
                    $postParam['content'] = Yii::$app->params['cdnHost'] . '/' . $model->attachment;
                    $chatRes = json_decode(Common::curlPost(Yii::$app->params['hisApiHost'] . Yii::getAlias('@hisapiFollowChat'), $postParam), true);
                }
                if ($chatRes['err_code'] == 0) {
                    $forceMessage = '操作成功';
                } else {
                    $forceMessage = '操作失败';
                }
                return [
                    'forceClose' => true,
                    'forceReload' => '#crud-datatable-pjax',
                    'forceMessage' => $forceMessage
                ];
            } else {
                $ret = [
                    'title' => "发送新消息",
                    'content' => $this->renderAjax('sendMessage', [
                        'model' => $model
                    ]),
                    'footer' => Html::button('关闭', ['class' => 'btn btn-cancel btn-form', 'data-dismiss' => "modal"]) .
                    Html::button('发送', ['class' => 'btn btn-default btn-form', 'type' => "submit"])
                ];
                return $ret;
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the Follow model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Follow the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Follow::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    protected function findPatientInfo($patientId) {
        $model = Patient::find()->select(['username', 'sex', 'iphone', 'birthday', 'patient_source', 'head_img'])->where(['id' => $patientId, 'spot_id' => $this->parentSpotId])->one();
        $model->birthTime = date('Y-m-d', $model->birthday);
        $model->hourMin = date('H:i', $model->birthday);
        return $model;
    }

}
