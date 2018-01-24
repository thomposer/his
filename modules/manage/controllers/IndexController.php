<?php

namespace app\modules\manage\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\common\base\BaseController;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotAcceptableHttpException;
use yii\web\Response;
use yii\helpers\Html;
use app\modules\spot\models\Spot;
/**
 * SiteController implements the CRUD actions for Service model.
 */
class IndexController extends BaseController
{
    public function behaviors()
    {
        
       $current =  [
           
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
       $parent = parent::behaviors();
       return ArrayHelper::merge($current, $parent);
    }
   
    /**
     * Lists all Service models.
     * @return mixed
     */
     public function actionIndex()
     {
         $parentSpotCode = $this->parentSpotCode;
         $defaultUrl = Url::to([Yii::$app->view->params['defaultUrl']]);
         if(Yii::$app->view->params['defaultUrl'] == null ){
             throw new NotAcceptableHttpException();
         }
        $returnUrl=  isset($_COOKIE['requestUrl'])?$_COOKIE['requestUrl']:'';
        if($returnUrl){
            setcookie('requestUrl', '', time()-1000, '/');
            return $this->redirect($returnUrl);
        }else{
            return $this->redirect($defaultUrl);
        }
     }
}
