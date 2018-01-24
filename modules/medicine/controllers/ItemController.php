<?php

namespace app\modules\medicine\controllers;

use Yii;
use app\modules\medicine\models\MedicineItem;
use app\modules\medicine\models\search\MedicineItemSearch;
use app\common\base\BaseController;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;

/**
 * ItemController implements the CRUD actions for MedicineItem model.
 */
class ItemController extends BaseController
{
    
    /**
     * Displays a single MedicineItem model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "MedicineItem #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                ];    
        }else{
            return $this->render('view',[
                'model' => $this->findModel($id)
            ]);
        }
    }

    

    /**
     * Finds the MedicineItem model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MedicineItem the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MedicineItem::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('你所请求的页面不存在');
        }
    }
}
