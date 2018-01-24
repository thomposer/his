<?php

namespace app\modules\room\controllers;

use Yii;
use app\common\base\BaseController;
use app\modules\spot_set\models\Room;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\Response;
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

    public function actionIndex() {
        $query = Room::find();
        $query->select([
            'id', 'clinic_name', 'treatment_time', 'clean_status'
        ]);
        $query->where(['clean_status' => 2, 'spot_id' => $this->spotId]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => ['treatment_time' => SORT_ASC],
                'attributes' => ['treatment_time']
            ]
        ]);
        return $this->render('index', [
                    'model' => new Room(),
                    'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFinish($id) {
        
        $request = Yii::$app->request;
        if($request->isAjax){
        
            /*
             *   Process for ajax request
             */
            $model = $this->findModel($id);
            $model->clean_status = 1;
            $model->record_id = 0;
            $res=$model->save();
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
             *   Process for non-ajax request
             */
            return $this->redirect(['index']);
        }
    }

    protected function findModel($id) {
        if (($model = Room::findOne(['id' => $id, 'spot_id' => $this->spotId])) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }

}
