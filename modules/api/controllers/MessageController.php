<?php

namespace app\modules\api\controllers;

use Yii;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use app\modules\outpatient\models\Report;
use yii\web\Response;
use app\modules\message\models\MessageCenter;
use yii\db\Query;
use app\modules\outpatient\models\InspectRecord;
use app\modules\inspect\models\InspectRecordUnion;
use app\modules\patient\models\Patient;
use yii\data\ActiveDataProvider;
use app\modules\inspect\models\Inspect;
use app\modules\triage\models\TriageInfo;
use yii\helpers\Html;

/**
 * 
 * @author 赠与
 * @property 消息中心接口
 */
class MessageController extends CommonController
{

    public function behaviors() {
        $parent = parent::behaviors();
        $current = [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'set-status' => ['post'],
                    'set-status-all' => ['post']
                ],
            ],
        ];
        return ArrayHelper::merge($current, $parent);
    }

    /*
     * @return 消息状态修改
     * @author1 曾瑜1.0
     * @author2 吴琴2.0修改
     * @time 2017年2月9日
     */

    public function actionSetStatus($id) {
        $spot_id = $this->spotId; //诊所id
        $user_id = $this->userInfo->id;

        $model = MessageCenter::find()->where(['spot_id' => $spot_id, 'id' => $id, 'user_id' => $user_id])->one();

        $model->status = 1;

        if ($model->save()) {
            return $this->redirect($model->url);
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    /*
     * @return 【一键清除】所有消息状态修改
     * @author 吴琴
     * @time 2017年2月9日
     */

    public function actionSetStatusAll() {

        $spot_id = $this->spotId; //诊所id
        $user_id = $this->userInfo->id;

        Yii::$app->db->createCommand()->update(MessageCenter::tableName(), ['status' => 1], ['spot_id' => $spot_id, 'user_id' => $user_id, 'category' => 1])->execute();

        Yii::$app->getSession()->setFlash('success', '清除成功');
        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionInspectWarnInfo($id = null) {
        $request = Yii::$app->request;

        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isPost && $id) {
            $model = InspectRecord::findOne(['id' => $id, 'spot_id' => $this->spotId]);
            $model->scenario = 'saveHandle';
            $model->handle_status = 1;
            $model->handle_time = time();
            $model->save();
            $cacheKey = Yii::getAlias('@doctorWarning') . $this->spotId . '_' . $this->userInfo->id;
            $doctorWarningCount = Yii::$app->cache->get($cacheKey);
            Yii::$app->cache->set($cacheKey, $doctorWarningCount - 1);
            return [
                'forceRedirect' => 'true',
                'forceClose' => true
            ];
        }
        $query = new Query();
        $query->from(['a' => InspectRecord::tableName()]);
        $query->select(['a.id', 'b.doctor_id', 'c.username', 'c.patient_number', 'c.sex', 'c.iphone', 'c.birthday', 'd.diagnosis_time']);
        $query->leftJoin(['b' => \app\modules\report\models\Report::tableName()], '{{a}}.record_id = {{b}}.record_id');
        $query->leftJoin(['c' => Patient::tableName()], '{{b}}.patient_id = {{c}}.id');
        $query->leftJoin(['d' => TriageInfo::tableName()], '{{a}}.record_id = {{d}}.record_id');
        $query->where(['a.notice_status' => 1, 'a.handle_status' => 2, 'b.doctor_id' => $this->userInfo->id]);
        $resultInfo = $query->one();
        if (!empty($resultInfo)) {
            $activeQuery = InspectRecordUnion::find()->select(['id', 'name', 'unit', 'reference', 'result', 'result_identification'])->where(['inspect_record_id' => $resultInfo['id'], 'result_identification' => ['HH', 'LL']]);
            $dataProvider = new ActiveDataProvider([
                'query' => $activeQuery,
                'sort' => [
                    'defaultOrder' => [
                        'id' => SORT_ASC
                    ],
                    'attributes' => ['id']
                ],
                'pagination' => [
                    'pageSize' => false
                ],
            ]);

            return [
                'title' => "报警",
                'content' => $this->renderAjax('@layoutWarnView', [
                    'patientInfo' => $resultInfo,
                    'dataProvider' => $dataProvider
                ]),
                'footer' => Html::a('已知晓，立即处理', ['@apiMessageInspectWarnInfo', 'id' => $resultInfo['id']], [
                    'class' => 'btn btn-default btn-form',
                    'data-method' => false,
                    'data-request-method' => 'post',
                    'role' => 'modal-remote'
                ])
            ];
        }
    }

    /**
     * 检验结果值报警
     */
    public function actionInspectWarn($id) {
        $request = Yii::$app->request;
        Yii::$app->response->format = Response::FORMAT_JSON;
        if ($request->isAjax) {
            $model = Inspect::findModel($id);
            $warning = Inspect::getWarning($model->record_id);
            $triageInfo = TriageInfo::getTriageInfo($model->record_id);
            $patientInfo = Patient::patientInfo(['username'], ['id' => $triageInfo['patient_id']]);
            if (Yii::$app->request->post()) {
                if ($model->notice_status == 2) {
                    $model->notice_status = 1;
                    $model->notice_user_id = $this->userInfo->id;
                    $model->notice_time = time();
                    $model->save();
                    //设置接诊医生的全局提醒的次数
                    $store = Yii::$app->cache->get(Yii::getAlias('@doctorWarning') . $this->spotId . '_' . $triageInfo['doctor_id']);
                    Yii::$app->cache->set(Yii::getAlias('@doctorWarning') . $this->spotId . '_' . $triageInfo['doctor_id'], $store ? $store + 1 : 1);
                }
                return [
                    'forceClose' => true,
                    'forceReloadPage' => true,
                    'forceMessage' => '操作成功'
                ];
            } else {
                $ret = [
                    'title' => "报警",
                    'content' => $this->renderAjax('@inspectIndexWarnView', [
                        'triageInfo' => $triageInfo,
                        'warning' => $warning,
                        'patientInfo' => $patientInfo,
                        'model' => $model,
                    ]),
                    'footer' => Html::button('通知医生', ['class' => 'btn  btn-default btn-form ', 'type' => "submit"])
                ];
                return $ret;
            }
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
