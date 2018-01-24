<?php

namespace app\modules\spot_set\controllers;


use app\modules\spot\models\SpotConfig;
use app\modules\spot_set\models\SpotType;
use Yii;

use app\modules\spot\models\search\SpotSearch;
use app\common\base\BaseController;

use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;
use yii\web\Response;


/**
 * IndexController implements the CRUD actions for Spot model.
 */
class SpotConfigController extends BaseController
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
     * Lists all Spot models.
     * @return mixed
     */
    public function actionConfig() {
        $model = SpotConfig::findOne(['spot_id' => $this->spotId]);
        if (!$model) {
            $model = new SpotConfig();
            $model->recipe_rebate=2;
        }
        if($model->load(Yii::$app->request->post())&&$model->save()){

/*
            if ($model->childCare) {//更新儿童保健

                SpotType::updateAll(['time' => $model->childCare], ['spot_id' => $this->spotId]);
            }*/
            Yii::$app->getSession()->setFlash('success','保存成功');
                return $this->redirect(['config']);

        }else {

            return $this->render('config', [
                'model' => $model,

            ]);
        }
    }

    public function actionRebateImg() {
        $request = Yii::$app->request;

        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "预览",
                'content' => $this->renderAjax('rebate-img', [
                    'id' => $request->get()['id'],
                ]),
            ];
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }





}
