<?php

namespace app\modules\triage\controllers;

use app\modules\spot\models\NursingRecordTemplate;
use Yii;
use app\modules\triage\models\NursingRecord;
use app\modules\triage\models\search\NursingRecordSearch;
use app\common\base\BaseController;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Json;
use app\modules\triage\controllers\TriageController;
use app\modules\user\models\User;

/**
 * NursingRecordController implements the CRUD actions for NursingRecord model.
 */
class NursingRecordController extends TriageController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Creates a new NursingRecord model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new NursingRecord();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            $recordId = Yii::$app->request->get('recordId');
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->save()) {

                return [
                    'title' => "完善患者信息",
                    'content' => $this->renderAjax('/triage/_modal', $this->getTriageModal($recordId))
                ];
            } else {
                return [
                    'title' => "新增护理项",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::a('取消', ['triage/modal', 'id' => $recordId, 'recordId' => $recordId], ['class' => 'btn btn-cancel btn-form', 'role' => 'modal-remote']) .
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
     * Updates an existing NursingRecord model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            $recordId = Yii::$app->request->get('recordId');
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->load($request->post()) && $model->save()) {
                return [
                    'title' => "完善患者信息",
                    'content' => $this->renderAjax('/triage/_modal', $this->getTriageModal($recordId))
                ];
            } else {
                if(is_numeric($model->execute_time)){  //从数据库出来为数字，验证失败为字符串不用转化
                    $model->execute_time = $model->execute_time != 0?date('Y-m-d H:i', $model->execute_time) :'';
                }
                return [
                    'title' => "编辑护理项",
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::a('取消', ['triage/modal', 'id' => $recordId, 'recordId' => $recordId], ['class' => 'btn btn-cancel btn-form', 'role' => 'modal-remote']) .
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
     * Delete an existing NursingRecord model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {

        $request = Yii::$app->request;
        $recordId = Yii::$app->request->get('recordId');
        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            $this->findModel($id)->delete();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "完善患者信息",
                'content' => $this->renderAjax('/triage/_modal', $this->getTriageModal($recordId))
            ];
        } else {
            /*
            *   Process for non-ajax request
            */
            return [
                'title' => "完善患者信息",
                'content' => $this->renderAjax('/triage/_modal', $this->getTriageModal($recordId))
            ];
        }

    }

    /**
     * @护理记录查看详情
     * @return array
     * @throws NotFoundHttpException
     */
    public function actionCareModal()
    {
        $request = Yii::$app->request;
        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $id = Yii::$app->request->get('id');
            $recordId = Yii::$app->request->get('recordId');
            $model = $this->findNursingRecordModel($id);

            $ret = [
                'title' => "查看护理项目",
                'content' => $this->renderAjax('_viewNursingItem', [
                    'model' => $model,
                    'recordId'=>$recordId
                ])
            ];

            return $ret;

        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Finds the NursingRecord model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return NursingRecord the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = NursingRecord::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

    protected function findNursingRecordModel($id)
    {
        $query = new Query();
        $query->from(['a' => NursingRecord::tableName()]);
        $query->select(['a.id', 'a.name', 'a.execute_time', 'a.creater_id', 'a.executor', 'a.content', 'a.create_time', 'b.username']);
        $query->leftJoin(['b' => User::tableName()], '{{a}}.creater_id = {{b}}.id');
        $query->where(['a.id' => $id,'a.spot_id'=>$this->spotId]);
        $model = $query->one();
        if ($model !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

}
